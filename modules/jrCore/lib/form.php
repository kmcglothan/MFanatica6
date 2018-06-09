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
 * @package Form Handling
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Set a Success/Error message in a form
 * @param string $type Type of message (error, notice, warning, success)
 * @param string $text Text of message
 * @param bool $strip Whether or not to strip HTML from message text
 * @return bool
 */
function jrCore_set_form_notice($type, $text, $strip = true)
{
    global $_post;
    $token = false;
    if (isset($_post['jr_html_form_token']) && jrCore_checktype($_post['jr_html_form_token'], 'md5')) {
        // We are on a SUBMIT page setting a notice - set our key
        $token                                     = $_post['jr_html_form_token'];
        $_SESSION['jrcore_form_notice_last_token'] = $token;
    }

    if ($text && jrCore_checktype($text, 'number_nz')) {
        $_lang = jrUser_load_lang_strings();
        if (isset($_lang["{$_post['module']}"][$text])) {
            $text = $_lang["{$_post['module']}"][$text];
        }
    }
    elseif ($strip) {
        $text = jrCore_strip_html($text);
    }
    $key = 'jrcore_form_notices';
    if (!isset($_SESSION[$key][$type]) || !is_array($_SESSION[$key][$type])) {
        $_SESSION[$key][$type] = array();
    }
    $_SESSION[$key][$type][] = array(
        'text'  => $text,
        'token' => $token
    );
    return true;
}

/**
 * Display a Success/Error message in a form
 * This function is used for showing a "notice" in a form - this would be a success message, fail message, etc.
 * @param bool $display If set to false, notices set will be returned as an array
 * @return mixed
 */
function jrCore_get_form_notice($display = true)
{
    $key = 'jrcore_form_notices';
    if (isset($_SESSION) && isset($_SESSION[$key]) && is_array($_SESSION[$key])) {

        // $_form will NOT be set if this is a brand new form, but if we have encountered an ERROR
        // in a form, it WILL be set on refresh (since jrCore_form_delete_session() has not been called)
        $_form = jrCore_form_get_session();

        foreach ($_SESSION[$key] as $type => $_entries) {
            foreach ($_entries as $k => $_entry) {

                // Only ever show 1 of any specific message
                if (strlen($_entry['token']) === 0 || (is_array($_form) && isset($_form['form_token']) && $_form['form_token'] == $_entry['token']) || (isset($_SESSION['jrcore_form_notice_last_token']) && $_SESSION['jrcore_form_notice_last_token'] == $_entry['token'])) {
                    if (!isset($_done["{$_entry['text']}"])) {
                        if (!$display) {
                            // We only return the LAST notice
                            $_out = array($type => $_entry['text']);
                            unset($_SESSION[$key][$type][$k]);
                            return $_out;
                        }
                        jrCore_page_notice($type, $_entry['text'], false);
                        $_done["{$_entry['text']}"] = 1;
                    }
                    unset($_SESSION[$key][$type][$k]);
                }

                if (count($_SESSION[$key][$type]) === 0) {
                    unset($_SESSION[$key][$type]);
                }

            }
        }
    }
    return true;
}

/**
 * Get ALL form notices that are currently set in the user's session
 * @return bool
 */
function jrCore_get_all_form_notices()
{
    $key = 'jrcore_form_notices';
    if (isset($_SESSION) && isset($_SESSION[$key]) && is_array($_SESSION[$key])) {
        return $_SESSION[$key];
    }
    return false;
}

/**
 * Clear ALL form notices set in a user session
 * @return bool
 */
function jrCore_clear_all_form_notices()
{
    $key = 'jrcore_form_notices';
    if (isset($_SESSION) && isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
    return true;
}

/**
 * Highlight a field in a form
 *
 * The jrCore_form_field_hilight function will "highlight" a form field that Jamroom
 * displays in the Control Panel.  This is used to draw attention to a form
 * field that might have an error.
 *
 * @param string $field Form field name ot highlight
 *
 * @return bool Returns true
 */
function jrCore_form_field_hilight($field)
{
    if (!isset($_SESSION['jrcore_form_field_highlight'])) {
        $_SESSION['jrcore_form_field_highlight'] = array();
    }
    $_SESSION['jrcore_form_field_highlight'][$field] = 1;
    return true;
}

/**
 * Create a new Form Session
 *
 * The jrCore_form_create function is used to create a new form
 * set in a module.
 *
 * @param array $_form form array form arguments consisting of one or more of the following:
 * name         - Form Name
 * action       - form action URL
 * submit_value - language string for submit button
 * reset        - set to true to enable "reset"
 * reset_value  - if set, "undo changes" button will show on form
 * cancel       - URL for cancel button (cancel will not show if url is not defined). "referrer" may be used to refer to previous page.
 * cancel_value - if set will be used instead of "cancel"
 * prompt       - Javascript alert dialog that pops up when the user presses submit asking for confirmation of submission
 * submit_modal - set to true to make form submit popup a modal window where the form target work takes place (i.e. progress)
 * error_msg    - when doing submitting a form, if form validation fails this will be the message.  Pass in a Lang ID.
 * success_msg  - when doing submitting a form, if form submission is successful, this will be the message.  Pass in a Lang ID.
 * values       - when doing an update, this is the ITEM values to use in the form.
 *
 * @return bool
 */
function jrCore_form_create($_form)
{
    global $_conf, $_post, $_user;
    $module          = $_post['module'];
    $_form['module'] = $module;

    // Create form name
    if (!isset($_form['name']{0})) {
        $_form['name'] = "{$module}_{$_post['option']}";
    }

    // Figure action URL
    $url = jrCore_get_module_url($module);
    if (!isset($_form['action'])) {
        $_form['action'] = "{$_conf['jrCore_base_url']}/{$url}/{$_post['option']}_save";
    }
    elseif (strpos($_form['action'], $_conf['jrCore_base_url']) !== 0) {
        $_form['action'] = ltrim(trim($_form['action']), '/');
        $_form['action'] = "{$_conf['jrCore_base_url']}/{$url}/{$_form['action']}";
    }

    // Expanded success message
    if (isset($_form['success_msg'])) {
        $_lang = jrUser_load_lang_strings();
        if (isset($_lang[$module]["{$_form['success_msg']}"])) {
            $_form['success_msg'] = $_lang[$module]["{$_form['success_msg']}"];
        }
    }

    // Check for our modal window - if we doing a modal window EVERY request
    // must have a unique modal token
    if (isset($_form['submit_modal'])) {
        $_form['modal_token'] = md5(microtime());
    }

    // Start our form - this will generate the unique form "token" that used
    // throughout the form form validation, etc.
    if (isset($_form['values']) && is_array($_form['values'])) {
        // Switch active profile ID
        if (!isset($_form['values']['_profile_id']) && isset($_form['values']['profile_id'])) {
            $_form['values']['_profile_id'] = (int) $_form['values']['profile_id'];
        }
        jrCore_set_flag('jrcore_form_create_values', $_form['values']);
        if (isset($_form['values']['_profile_id']) && $_form['values']['_profile_id'] != $_SESSION['user_active_profile_id'] && jrProfile_is_profile_owner($_form['values']['_profile_id'])) {
            $_user = jrProfile_change_to_profile($_form['values']);
        }
    }

    // Make sure cancel URL is correct if we get one
    if (jrCore_get_flag('jrcore_is_profile_referrer')) {
        if ((!isset($_form['cancel_detect']) || $_form['cancel_detect'] === true) && isset($_form['cancel']) && jrCore_checktype($_form['cancel'], 'url')) {
            $rurl = jrCore_get_local_referrer();
            if ($rurl && !strpos($rurl, "/{$_user['profile_url']}")) {
                // We may have changed profiles - correct URL
                $_form['cancel'] = $rurl;
            }
        }
    }

    $tok            = jrCore_form_begin($_form['name'], $_form['action'], $_form);
    $_form['token'] = $tok;

    // See if we have VALUES being passed in.  a "value" key will contain
    // the default value for a field, or the existing value for an item
    if (isset($_form['values']) && is_array($_form['values'])) {
        // Show our pending notice for this module/item
        jrCore_show_pending_notice($_form['module'], $_form['values']);
    }
    elseif (!jrUser_is_admin() && isset($_post['option']) && strpos(' ' . $_post['option'], 'create')) {
        // See if this is a create form and we need to show our pending notice
        $_pn = jrCore_get_registered_module_features('jrCore', 'pending_support');
        if ($_pn && isset($_pn[$module]) && isset($_user["quota_{$module}_pending"]) && $_user["quota_{$module}_pending"] > 0) {
            if (!isset($_user["quota_{$module}_max_items"]) || $_user["quota_{$module}_max_items"] == 0 || (isset($_user["profile_{$module}_item_count"]) && $_user["profile_{$module}_item_count"] < $_user["quota_{$module}_max_items"])) {
                $_ln = jrUser_load_lang_strings();
                jrCore_set_form_notice('notice', $_ln['jrCore'][88]);
            }
        }
    }

    // Save to our URL stack for canceling.
    jrCore_save_url_history();

    // Save our form session and return CSRF token
    jrCore_form_create_session($tok, $_form);
    jrCore_get_form_notice();

    return $tok;
}

/**
 * Add a form field to a Form Session
 *
 * The jrCore_form_field_create function is used to add a new form field
 * to an open form created by jrFormCreate
 *
 * @param array $_field form arguments consisting of one or more of the following:
 * name          - Name of the form field to use in the HTML form
 * label         - Language string for Form Field label
 * value         - value for field on form load
 * type          - type of form field (text, select, etc.)
 * help          - 'Help' text for field that tells the user what the field should contain
 * validate      - Type of JS jrCore_checktype validation to perform on field before submitting
 * error_msg     - error message to show if invalid data is placed in field
 * @param string $module Module Name
 * @param string $form_name Form Name to add field to
 * @param bool $designer Set to false to disable checking for designer form field
 *
 * @return bool Returns true
 */
function jrCore_form_field_create($_field, $module = null, $form_name = null, $designer = true)
{
    global $_post;
    // Check Profile Quota and User Group permissions
    if (isset($_field['group']) && strlen($_field['group']) > 0) {
        // Check for multiple groups
        $_gr = explode(',', $_field['group']);
        if (is_array($_gr)) {
            $show = false;
            foreach ($_gr as $grp) {
                if (jrCore_user_is_part_of_group($grp)) {
                    $show = true;
                    break;
                }
            }
            if (!$show) {
                return true;
            }
        }
    }

    // See if we are showing for a specific Quota ID
    if (isset($_field['quota_id']) && jrCore_checktype($_field['quota_id'], 'number_nz')) {
        if (!jrUser_in_quota($_field['quota_id'])) {
            return true;
        }
    }

    // We need to see if we have a designer form field OVERRIDING this field
    if ($designer && isset($_field['name']) && (!isset($_field['form_designer']) || $_field['form_designer'] !== false)) {
        $_tmp = jrCore_get_designer_form_fields($_post['module'], $_post['option']);
        if ($_tmp && is_array($_tmp) && isset($_tmp["{$_field['name']}"]) && $_tmp["{$_field['name']}"]['created'] != $_tmp["{$_field['name']}"]['updated']) {
            foreach ($_tmp["{$_field['name']}"] as $k => $v) {
                if (strlen($v) > 0) {
                    $_field[$k] = $v;
                }
            }
        }
    }

    // See if we have an active form session
    $_form = jrCore_form_get_session();

    // Setup module and form_name if we did not get them
    if (is_null($module) || !isset($module) || $module === false) {
        $module = (isset($_form['form_params']['module'])) ? $_form['form_params']['module'] : $_post['module'];
    }
    if (is_null($form_name) || !isset($form_name) || $form_name === false) {
        $form_name = $_form['form_params']['name'];
    }

    // Make sure field type is valid
    $_fld = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'form_field');
    foreach ($_tmp as $m => $_v) {
        foreach ($_v as $k => $v) {
            $_fld[$k] = $m;
        }
    }
    if (!isset($_fld["{$_field['type']}"])) {
        // bad field type
        jrCore_logger('CRI', "invalid form type: {$_field['type']} for field: {$form_name}/{$_field['name']} - field type is not registered");
        return true;
    }

    // for "required" the developer can pass in "true" or "1" to indicate
    // the field is required - make sure we have the intval here
    if (isset($_field['required']) && ($_field['required'] === true || $_field['required'] == 'true' || $_field['required'] == '1' || $_field['required'] == 'on')) {
        $_field['required'] = 1;
    }
    else {
        $_field['required'] = 0;
    }

    // Add providing module into field info
    $_field['module'] = $_fld["{$_field['type']}"];

    // Let other modules see what we are building
    $_args  = array(
        'module'    => $module,
        'form_name' => $form_name,
        'designer'  => $designer
    );
    $_field = jrCore_trigger_event('jrCore', 'form_field_create', $_field, $_args);

    // Expand help...
    if (isset($_field['help']) && function_exists($_field['help'])) {
        $_field['help'] = $_field['help']();
    }

    // All form field "plugins" can have several functions:
    // "display" - this is run when the field is displayed in the form - i.e. it creates the proper HTML
    // "validate" - this is called when the posted value is received so it can be validated
    // "prepare" - called before the "display" function so any pre-existing data can be formatted (if needed)

    // Define display function
    $func = $_fld["{$_field['type']}"] . "_form_field_{$_field['type']}_display";

    // Make sure we have our session container for form validation/saving
    jrCore_form_add_field_to_session($_form['form_token'], $_field);

    // Expand language strings
    $_lang = jrUser_load_lang_strings();
    $_todo = array('placeholder', 'label', 'sublabel', 'help', 'error_msg');
    foreach ($_todo as $lbl) {
        if (isset($_field[$lbl]) && isset($_lang[$module]["{$_field[$lbl]}"])) {
            $_field[$lbl] = $_lang[$module]["{$_field[$lbl]}"];
        }
    }

    // See if we have a saved previously posted form
    if (isset($_field['name']) && isset($_form['form_saved']["{$_field['name']}"])) {
        $_field['value'] = jrCore_entity_string($_form['form_saved']["{$_field['name']}"]);
    }
    elseif (!isset($_field['value'])) {
        $_temp = jrCore_get_flag('jrcore_form_create_values');
        if ($_temp && isset($_temp["{$_field['name']}"])) {
            $_field['value'] = jrCore_entity_string($_temp["{$_field['name']}"]);
        }
    }
    else {
        if (is_array($_field['value'])) {
            foreach ($_field['value'] as $k => $v) {
                $_field['value'][$k] = jrCore_entity_string($v);
            }
        }
        else {
            $_field['value'] = jrCore_entity_string($_field['value']);
        }
    }

    // Get allowed attributes if any are passed
    $_attr = jrCore_get_form_field_attributes($_field['type'], $_field);

    // Check for highlighting
    if (isset($_post['hl'])) {
        if (is_array($_post['hl']) && in_array($_field['name'], $_post['hl'])) {
            $_attr['onfocus'] = "$('#{$_field['name']}').removeClass('field-hilight');";
        }
        elseif ($_post['hl'] == $_field['name']) {
            $_attr['onfocus'] = "$('#{$_field['name']}').removeClass('field-hilight');";
        }
    }
    $func($_field, $_attr);
    return true;
}

/**
 * Complete processing in a form result handler
 *
 * @param string $url URL to redirect browser to (default is 'referrer')
 * @return null
 */
function jrCore_form_result($url = 'referrer')
{
    global $_post, $_conf, $_user;

    // true/false to run "form_result" trigger
    $show = true;

    // See if we are doing a normal form submit or an AJAX form submit
    if (jrCore_is_ajax_request()) {

        $_out = array();
        if ($url != 'referrer') {
            // If we have a redirect, we don't "clear" any notices or
            // error fields so the receiver can pick them up.
            $_out['redirect'] = $url;
        }
        else {
            $_tmp = jrCore_get_all_form_notices();
            if ($_tmp && is_array($_tmp)) {
                $_out['notices'] = array();
                foreach ($_tmp as $type => $_entries) {
                    if (count($_entries) > 0) {
                        foreach ($_entries as $k => $_entry) {
                            $_out['notices'][] = array('type' => $type, 'text' => $_entry['text']);
                            if ($type == 'error') {
                                $show = false;
                            }
                        }
                    }
                }
                jrCore_clear_all_form_notices();
            }
            else {
                $_out['notices'][] = array('type' => 'error', 'text' => 'no form message set!');
                $show              = false;
            }
            if (isset($_SESSION['jrcore_form_field_highlight']) && is_array($_SESSION['jrcore_form_field_highlight'])) {
                $_out['error_fields'] = array();
                foreach ($_SESSION['jrcore_form_field_highlight'] as $field => $num) {
                    $_out['error_fields'][] = "#{$field}";
                }
                unset($_SESSION['jrcore_form_field_highlight']);
            }
        }
        // Send trigger to let other modules know we have finished with this form
        // We only send this if there are no errors in the form target
        if ($show) {
            $_tmp = jrCore_get_flag('jrcore_form_validate_post_values');
            if (!$_tmp) {
                $_tmp = array();
            }
            jrCore_trigger_event('jrCore', 'form_result', $_tmp);
        }
        session_write_close();
        jrCore_json_response($_out, true, false);
    }

    // Fall through for normal redirect
    $show = true;
    $_tmp = jrCore_get_all_form_notices();
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $type => $_entries) {
            if ($type == 'error') {
                $show = false;
                break;
            }
        }
    }

    // Send trigger to let other modules know we have finished with this form
    // We only send this if there are no errors in the form target
    if ($show) {
        jrCore_trigger_event('jrCore', 'form_result', $_post);
    }

    if ($url == 'referrer' || !jrCore_checktype($url, 'url')) {
        $url = jrCore_get_local_referrer();
    }
    elseif ($url == 'delete_referrer') {
        // If our referrer was from the item details page, we refresh back to the main item list
        $url = jrCore_get_local_referrer();
        if (strpos($url, "/{$_post['module_url']}/{$_post['id']}")) {
            jrCore_location("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
        }
        if ($url = jrUser_get_saved_url_location()) {
            jrCore_location($url);
        }
        jrCore_location('referrer');
    }
    return jrCore_location($url);
}

/**
 * Send a response in JSON format
 * @param array $_data Array of data to send as JSON k/v pairs
 * @param bool $exit True to exit process on send
 * @param bool $strip_tags True to strip all HTML from response
 * @param bool $process_exit set to FALSE to not trigger process_exit event
 * @return bool
 */
function jrCore_json_response($_data, $exit = true, $strip_tags = true, $process_exit = true)
{
    global $_post;
    $_data = json_encode($_data);
    if ($strip_tags) {
        $_data = strip_tags($_data);
    }
    jrCore_set_custom_header('Content-Type: application/json');
    if ($exit) {
        jrCore_process_exit_delete_profile_cache();
        jrCore_send_response_and_detach($_data);
        if ($process_exit) {
            jrCore_trigger_event('jrCore', 'process_exit', $_post);
        }
        jrCore_db_close();
        exit;
    }
    ob_start();
    echo $_data;
    ob_end_flush();
    return true;
}

/**
 * Send a response to the browser and detach
 * @param string $data Data to send to browser
 * @param bool $done_trigger set to TRUE to trigger process_done
 * @return bool
 */
function jrCore_send_response_and_detach($data = null, $done_trigger = false)
{
    global $_conf;
    $_tmp = array();
    if (!headers_sent()) {
        $cont = false;
        $_tmp = jrCore_get_flag('jrcore_set_custom_header');
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $header) {
                if (stripos($header, 'Content-Length') !== 0) {
                    header($header);
                    if (stripos($header, 'Content-Type') === 0) {
                        $cont = true;
                    }
                }
            }
        }
        else {
            $_tmp = array();
        }
        if (!$cont) {
            header("Content-Type: text/html; charset=utf-8");
        }
        if (!isset($_conf['jrCore_disable_xframe'])) {
            header('X-Frame-Options: SAMEORIGIN');
        }
    }

    // Close any open session
    if (isset($_SESSION)) {
        session_write_close();
    }

    // Send output
    // THE ORDER OF THE FOLLOWING STATEMENTS is critical for it
    // to work properly on mod_php, CGI/FastCGI, and FPM - DO NOT CHANGE!
    $length = 0;

    @ob_end_clean();
    header('Connection: close');
    ignore_user_abort();
    if (strlen($data) > 0) {
        ob_start();
        ob_start('ob_gzhandler');
        echo $data;
        ob_end_flush();
        $length = ob_get_length();
        header('Content-Length: ' . $length);
        ob_end_flush();
    }
    @ob_end_flush();
    @ob_flush();
    @flush();

    // PHP-FPM only
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    // NOTE: Client is detached at this point!
    $_tmp['results_length'] = $length;
    if ($done_trigger) {
        jrCore_trigger_event('jrCore', 'process_done', $_tmp);
    }
    else {
        jrCore_trigger_event('jrCore', 'results_sent', $_tmp);
    }
    jrCore_set_flag('jrcore_client_is_detached', true);
    return true;
}

/**
 * @ignore
 * Check if a value is within allowed min/max values
 * @param string $validate either "number" or "string"
 * @param string $value value string value to check
 * @param int $min Minimum value
 * @param int $max Maximum value
 * @param string $prefix Prefix to Error Message
 * @return bool
 */
function jrCore_is_valid_min_max_value($validate, $value, $min, $max, $prefix)
{
    $len_idx  = false;
    $len_msg  = false;
    $len_type = jrCore_checktype('', $validate, false, true);
    if ($len_type) {
        switch ($len_type) {
            case 'float':
                if ($min > 0 && floatval($value) < floatval($min)) {
                    $len_idx = 100;
                    $len_msg = $min;
                }
                elseif ($max > 0 && floatval($value) > floatval($max)) {
                    $len_idx = 101;
                    $len_msg = $max;
                }
                break;

            case 'number':
                $len = intval($value);
                if ($min > 0 && $len < $min) {
                    $len_idx = 100;
                    $len_msg = $min;
                }
                elseif ($max > 0 && $len > $max) {
                    $len_idx = 101;
                    $len_msg = $max;
                }
                break;

            case 'string':
                $len = strlen($value);
                if ($min > 0 && $len < $min) {
                    $len_idx = 102;
                    $len_msg = $min;
                }
                elseif ($max > 0 && $len > $max) {
                    $len_idx = 103;
                    $len_msg = $max;
                }
                break;
        }
        // See if we have a length error
        if ($len_idx) {
            $_ln = jrUser_load_lang_strings();
            jrCore_set_form_notice('error', $prefix . str_replace('%1', $len_msg, $_ln['jrCore'][$len_idx]));
            return false;
        }
    }
    return true;
}

/**
 * Validate a submitted form
 * The jrCore_form_validate function will "validate" posted form
 * values to ensure they are of the proper "type"
 * @param array $_post Posted parameters from jrCore_parse_url();
 * @return bool
 */
function jrCore_form_validate(&$_post)
{
    global $_user;
    ignore_user_abort();

    // Make sure we get a valid form token
    if (!isset($_post['jr_html_form_token']) || !jrCore_checktype($_post['jr_html_form_token'], 'md5')) {
        jrCore_set_form_notice('error', 'Form Validation missing - please refresh and try again.');
        jrCore_form_result();
    }

    // Next, get our form fields so we can do our validation
    $_rt = jrCore_form_get_session($_post['jr_html_form_token']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid Form Validation received - please refresh and try again.');
        jrCore_form_result();
    }

    // jrCore_form_validate will be called TWO times on each form request:
    // - once for the client side AJAX form validator (no file fields though)
    // - once on the actual form POST to the form "save" handler.
    // Since no file fields are present in the initial AJAX submission, we're not
    // going to run our event trigger until we have the actual data.
    if (isset($_rt['form_validated']) && $_rt['form_validated'] == '1') {
        // We've already been through this form from the client side - trigger
        $_post = jrCore_trigger_event('jrCore', 'form_validate_init', $_post, $_rt);
    }

    // Save posted data to form - if we run into any errors we will use
    // this data to replace the values in the form field so we don't lose
    // any of their data
    jrCore_form_save_session($_post['jr_html_form_token'], $_post);

    // Make sure we have some fields
    if (!isset($_rt['form_fields']) || !is_array($_rt['form_fields'])) {
        // AJAX response (for client side validation)
        if (jrCore_is_ajax_request()) {
            return json_encode(array('OK' => 1));
        }
        return true;
    }

    // Make sure language strings are loaded up
    $_lang = jrUser_load_lang_strings();

    // Get registered field types
    $_fld = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'form_field');
    foreach ($_tmp as $m => $_v) {
        foreach ($_v as $k => $v) {
            $_fld[$k] = $m;
        }
    }

    // Validate each field
    foreach ($_rt['form_fields'] as $k => $_valid) {

        // Make sure the field is active
        if (isset($_valid['active']) && $_valid['active'] == '0') {
            // Field is not active - make sure nothing is posted for it
            unset($_post["{$_valid['name']}"]);
            continue;
        }

        // Check for Spam Bot protection
        if (isset($_valid['type']) && $_valid['type'] == 'checkbox_spambot') {
            jrCore_set_flag('jrcore_form_validate_checkbox_spambot', $_valid['name']);
            // spam bot field is always required
            $_valid['required'] = 'on';
        }

        // field permissions - if we receive a field in our post
        // that is an admin/master only field, and the validating user is NOT
        // an admin/master user, we unset that field.  We should NOT
        // even get here since jrCore_page_display() would not have displayed the
        // field in the first place, but we need to check here to be sure
        if (isset($_valid['group']{0})) {
            if (!jrCore_user_is_part_of_group($_valid['group'])) {
                unset($_rt['form_fields'][$k]);
                continue;
            }
        }

        // Some field types (checkbox, file, etc.) need some massaging before
        // falling into the validation loop below
        if (!isset($_fld["{$_valid['type']}"])) {
            // bad field type
            jrCore_logger('CRI', "invalid form type for field: {$_rt['form_name']}/{$_valid['name']} - field type is not registered");
            continue;
        }

        // Form types can check the field
        $afunc = $_fld["{$_valid['type']}"] . "_form_field_{$_valid['type']}_params";
        if (function_exists($afunc)) {
            $_valid = $afunc($_valid, $_post);
        }

        // Some fields need "assembly" since they may be posted in multiple parts
        $afunc = $_fld["{$_valid['type']}"] . "_form_field_{$_valid['type']}_assembly";
        if (function_exists($afunc)) {
            $_post = $afunc($_valid, $_post);
        }

        // Skip any where validation has been purposely disabled...
        if (!isset($_valid['validate']) || (isset($_valid['validate']) && ($_valid['validate'] == 'false' || $_valid['validate'] === false))) {
            // validation is turned off on this field
            continue;
        }

        // Make sure we have a valid field_required...
        if (!isset($_valid['required']) || $_valid['required'] == '0' || $_valid['required'] == 0) {
            $_valid['required'] = false;
        }
        else {
            if ($_valid['required'] == '1' || $_valid['required'] == 'true' || $_valid['required'] === true || $_valid['required'] == 'on') {
                $_valid['required'] = true;
            }
            else {
                $_valid['required'] = false;
            }
        }
        if (!$_valid['required']) {
            // See if this type is giving us an "is_empty" function
            $efunc = $_fld["{$_valid['type']}"] . "_form_field_{$_valid['type']}_is_empty";
            if (function_exists($efunc)) {
                if ($efunc($_valid, $_post)) {
                    // We are empty but not required...
                    continue;
                }
            }
            else {
                if (isset($_valid['name']) && (!isset($_post["{$_valid['name']}"]) || strlen($_post["{$_valid['name']}"]) === 0)) {
                    // Empty and not required...
                    continue;
                }
            }
        }

        // Check for label lang string
        if (!isset($_valid['label'])) {
            $_valid['label'] = '';
        }
        if (isset($_rt['form_params']['module']) && isset($_valid['label']) && isset($_lang["{$_rt['form_params']['module']}"]["{$_valid['label']}"])) {
            $_valid['label'] = $_lang["{$_rt['form_params']['module']}"]["{$_valid['label']}"];
        }

        // Make sure we have a good active profile ID
        if (jrUser_is_logged_in()) {

            $pid = $_user['user_active_profile_id'];
            if (isset($_post['jr_html_form_profile_id']) && jrCore_checktype($_post['jr_html_form_profile_id'], 'number_nz') && $_post['jr_html_form_profile_id'] != $pid) {
                if (jrProfile_is_profile_owner($_post['jr_html_form_profile_id'])) {
                    $pid = (int) $_post['jr_html_form_profile_id'];
                    // We have posted from a different profile_id then the current ACTIVE profile_id - change
                    if ($_pr = jrCore_db_get_item('jrProfile', $pid)) {
                        $_user = jrProfile_change_to_profile($_pr);
                    }
                }
            }

            // Check for UNIQUE
            if (isset($_valid['unique']) && $_valid['unique'] != 'off' && $_valid['unique'] != ' false' && $_valid['unique'] !== false && isset($_post["{$_valid['name']}"]) && strlen($_post["{$_valid['name']}"]) > 0) {
                // we have to make sure there are NO OTHER entries for this profile, for this module with this key => value
                // If this is an UPDATE form, we will have $_rt['form_params']['values'] - we need to make sure that a
                // search on the NEW value comes back empty
                $_params = array(
                    'search'         => array(
                        "_profile_id = {$pid}",
                        "{$_valid['name']} = " . $_post["{$_valid['name']}"]
                    ),
                    'limit'          => 1,
                    'skip_triggers'  => true,
                    'privacy_check'  => false,
                    'ignore_pending' => true
                );
                if (strlen($_valid['unique']) > 0 && $_valid['unique'] != 'on' && $_valid['unique'] != ' true' && $_valid['unique'] !== true) {
                    $_params['search'][] = "{$_valid['name']} != {$_valid['unique']}";
                }
                $_rt = jrCore_db_search_items($_rt['form_params']['module'], $_params);
                if ($_rt && is_array($_rt)) {
                    jrCore_set_form_notice('error', "{$_lang['jrCore'][65]} {$_valid['label']} {$_lang['jrCore'][66]}");
                    jrCore_form_field_hilight($_valid['name']);
                    jrCore_form_result();
                }
            }
        }

        // Create our combined error messages
        if (isset($_valid['validate']{0})) {
            // Check for a provided error_msg
            if (isset($_valid['error_msg'])) {
                $e_msg = (is_numeric($_valid['error_msg']) && isset($_lang["{$_rt['form_params']['module']}"]["{$_valid['error_msg']}"])) ? $_lang["{$_rt['form_params']['module']}"]["{$_valid['error_msg']}"] : $_valid['error_msg'];
            }
            else {
                $err_s = jrCore_checktype('', $_valid['validate'], true);
                if (jrCore_checktype($err_s, 'number_nz')) {
                    $err_s = $_lang['jrCore'][$err_s];
                }
                $e_msg = "{$_lang['jrCore'][30]}{$_valid['label']}{$_lang['jrCore'][31]} " . $err_s;
            }
            // special check for "not_empty" so it prints the correct number of chars..
            if ($_valid['validate'] == 'not_empty' && (!isset($_valid['min']) || !jrCore_checktype($_valid['min'], 'number_nn'))) {
                $_valid['min'] = 1;
            }
        }
        else {
            $e_msg = isset($_valid['label']) ? "{$_lang['jrCore'][30]}{$_valid['label']}&quot;" : $_lang['jrCore'][30];
        }

        // Validate if given a validation function
        $vfunc = $_fld["{$_valid['type']}"] . "_form_field_{$_valid['type']}_validate";
        if (function_exists($vfunc)) {
            $_post = $vfunc($_valid, $_post, $e_msg);
            if (!$_post) {
                jrCore_form_field_hilight($_valid['name']);
                // If our field type is DATE, we have to do a full page refresh or the date jQuery widget will be attached in the wrong location
                switch ($_valid['type']) {
                    case 'date':
                    case 'datetime':
                        $ref = jrCore_get_local_referrer();
                        jrCore_form_result($ref);
                        break;
                }
                jrCore_form_result();
            }
        }
        else {
            // Check for min/max values
            if ((isset($_valid['min']) && $_valid['min'] > 0) || (isset($_valid['max']) && $_valid['max'] > 0)) {
                $min = (isset($_valid['min'])) ? $_valid['min'] : 0;
                $max = (isset($_valid['max'])) ? $_valid['max'] : 0;
                if (!@jrCore_is_valid_min_max_value($_valid['validate'], $_post["{$_valid['name']}"], $min, $max, $e_msg)) {
                    // Our error message is set in jrCore_check_valid_min_max_value()
                    jrCore_form_field_hilight($_valid['name']);
                    jrCore_form_result();
                }
            }
            // Default validation routine using checktype
            if (!jrCore_checktype($_post["{$_valid['name']}"], $_valid['validate'])) {
                jrCore_set_form_notice('error', $e_msg);
                jrCore_form_field_hilight($_valid['name']);
                jrCore_form_result();
            }
            // We do not allow HTML in any non-editor fields, or fields where
            // the validation type is NOT "allowed_html"
            if ($_valid['validate'] != 'allowed_html' && (!isset($_user['quota_jrCore_active_formatters']) || !strpos($_user['quota_jrCore_active_formatters'], '_bbcode') || !stripos($_post["{$_valid['name']}"], '[code]'))) {
                $_post["{$_valid['name']}"] = strip_tags($_post["{$_valid['name']}"]);
            }
        }
    }

    // On our initial AJAX validation call, set the "form_validated" flag
    // to "1" to let the next pass know we've already been here.
    if (isset($_rt['form_validated']) && $_rt['form_validated'] != '1') {
        jrCore_form_mark_as_validated($_rt['form_token']);
    }
    else {
        // If this form included a spam bot check, remove it from post so
        // it is not saved to a datastore
        $tmp = jrCore_get_flag('jrcore_form_validate_checkbox_spambot');
        if ($tmp) {
            unset($_post[$tmp]);
        }
        // We're on our second pass through the validator - fire exit trigger
        jrCore_set_flag('jrcore_form_validate_post_values', $_post);
        jrCore_trigger_event('jrCore', 'form_validate_exit', $_post);

        if (isset($_post['jr_html_form_profile_id']) && jrCore_checktype($_post['jr_html_form_profile_id'], 'number_nz') && $_post['jr_html_form_profile_id'] != $_user['user_active_profile_id']) {
            // Looks like we have a situation where the user may have viewed other
            // profiles they can control while in the middle of this form - we need
            // to set the profile back to the profile_id they were using when they started
            if (jrProfile_is_profile_owner($_post['jr_html_form_profile_id'])) {
                // Looks like this user has access to the profile - switch
                $_pr = jrCore_db_get_item('jrProfile', $_post['jr_html_form_profile_id']);
                if ($_pr && is_array($_pr)) {
                    $_user = jrProfile_change_to_profile($_pr);
                }
            }
        }
    }

    // AJAX response (for client side validation)
    if (jrCore_is_ajax_request()) {
        return json_encode(array('OK' => 1));
    }
    return $_rt['form_token'];
}

/**
 * @ignore
 * Get additional form field attributes
 *
 * @param string $type Type of form field
 * @param array $_field Array of field information
 * @return array
 */
function jrCore_get_form_field_attributes($type, $_field)
{
    $_out = array();
    $_fld = array();
    $_tmp = jrCore_get_registered_module_features('jrCore', 'form_field');
    foreach ($_tmp as $m => $_v) {
        foreach ($_v as $k => $v) {
            $_fld[$k] = $m;
        }
    }
    if (!isset($_fld) || !is_array($_fld) || !isset($_fld[$type])) {
        return $_out;
    }
    $func = $_fld[$type] . "_form_field_{$type}_attributes";
    if (function_exists($func)) {
        $_att = $func();
        if (isset($_att) && is_array($_att)) {
            foreach ($_att as $val) {
                if (isset($_field[$val])) {
                    $_out[$val] = $_field[$val];
                }
            }
        }
    }
    return $_out;
}

/**
 * Get all posted data that can be saved to the Data Store for a module
 * @param string $module Module that has registered a designer form view
 * @param string $view View to get form fields for
 * @param array $_data $_REQUEST data to parse (default is $_post)
 * @return mixed
 */
function jrCore_form_get_save_data($module, $view, $_data)
{
    global $_post;
    // We need to make sure that what we return only consists of
    // data submitted directly from the form.
    $_rt = jrCore_form_get_session($_post['jr_html_form_token']);
    if (!$_rt || !is_array($_rt) || !isset($_rt['form_fields']) || !is_array($_rt['form_fields'])) {
        return false;
    }
    $_out = array();
    foreach ($_rt['form_fields'] as $v) {
        if (isset($v['name'])) {
            $name = $v['name'];
            // The field name MUST BEGIN with the module's DataStore Prefix
            $prfx = jrCore_db_get_prefix($module);
            if (isset($_data[$name]) && strpos($name, $prfx) === 0) {
                $_out[$name] = $_data[$name];
            }
        }
    }
    if (count($_out) > 0) {
        $_args = array(
            'module' => $module,
            'view'   => $view
        );
        $_out  = jrCore_trigger_event('jrCore', 'get_save_data', $_out, $_args);
        return $_out;
    }
    return false;
}

/**
 * @ignore
 * jrCore_form_field_get_hilight
 * @param string $field Form field name to high light
 * @return mixed
 */
function jrCore_form_field_get_hilight($field)
{
    global $_post;
    if (isset($_SESSION['jrcore_form_field_highlight'][$field])) {
        unset($_SESSION['jrcore_form_field_highlight'][$field]);
        return ' field-hilight';
    }
    elseif (isset($_post['hl'])) {
        if (is_array($_post['hl']) && in_array($field, $_post['hl'])) {
            return ' field-hilight';
        }
        elseif ($_post['hl'] == $field) {
            return ' field-hilight';
        }
    }
    return false;
}

/**
 * Get current form Tab Index from display order of form elements
 *
 * @param array $_field Field info
 * @return int
 */
function jrCore_form_field_get_tab_index($_field)
{
    if (isset($_field['tabindex']) && jrCore_checktype($_field['tabindex'], 'number_nz')) {
        return $_field['tabindex'];
    }
    $index = jrCore_get_flag('jr_form_tab_index');
    if (!$index) {
        $index = 0;
    }
    $index++;
    jrCore_set_flag('jr_form_tab_index', $index);
    $_field['tabindex'] = $index;
    return $index;
}

/**
 * Add Upload Progress Meter support to a form field definition
 *
 * @param array $_field Array of Form field information to add Progress Meter support to
 * @param string $allowed Comma separated list of file extensions to allow
 * @param int $max_size Maximum allowed upload size in bytes
 * @param bool $multiple Set to true to allow multiple file uploads for the field
 * @return mixed Returns $_field updated with new entries
 */
function jrCore_enable_meter_support($_field, $allowed = 'mp3', $max_size = 2097152, $multiple = false)
{
    global $_user, $_post, $_conf;
    $_lang = jrUser_load_lang_strings();

    // Get current session token
    $_sess = jrCore_form_get_session();
    $token = (isset($_sess['form_token'])) ? $_sess['form_token'] : '';

    // If this meter is enabled for multiple item support, we have to see if the profile quota is
    // limiting the number of items allowed to upload and block if they go over
    if ($multiple) {
        if (!jrUser_is_admin() && isset($_user["quota_{$_post['module']}_max_items"]) && $_user["quota_{$_post['module']}_max_items"] > 0) {
            // Looks like we are limiting the number of items - see how many this user has currently created on their profile
            if (isset($_user["profile_{$_post['module']}_item_count"]) && intval($_user["profile_{$_post['module']}_item_count"]) > $_user["quota_{$_post['module']}_max_items"]) {
                return false; // not allowed
            }
            $multiple = (int) ($_user["quota_{$_post['module']}_max_items"] - $_user["profile_{$_post['module']}_item_count"]);
        }
    }

    // For multiple uploads we can have:
    // false - multiple uploads NOT allowed (single)
    // true - unlimited uploads
    // (int) - number of allowed uploads
    if (jrCore_checktype($multiple, 'number_nz')) {
        $multi = 'true';
        $maxup = (int) $multiple;
    }
    else {
        $maxup = 1;
        $multi = ($multiple) ? 'true' : 'false';
        if ($multi) {
            $maxup = 0;
        }
    }
    $debug = 'false';
    if (jrCore_is_developer_mode()) {
        $debug = 'true';
    }
    // Initialize field
    $curl = jrCore_get_module_url('jrCore');
    // Get our button text
    if (isset($_field['text'])) {
        if (jrCore_checktype($_field['text'], 'number_nz') && isset($_lang["{$_post['module']}"]["{$_field['text']}"])) {
            $text = $_lang["{$_post['module']}"]["{$_field['text']}"];
        }
        else {
            $text = $_field['text'];
        }
    }
    else {
        $text = ($multiple) ? $_lang['jrCore'][45] : $_lang['jrCore'][43];
    }
    $_field['text'] = $text;

    // Figure out our upload token - see if one has already been created for us in this form
    $tokn = false;
    if (isset($_sess['form_fields']) && is_array($_sess['form_fields'])) {
        foreach ($_sess['form_fields'] as $_ff) {
            if (isset($_ff['name']) && $_ff['name'] == 'upload_token') {
                $tokn = $_ff['value'];
            }
        }
    }
    if (!$tokn) {
        $tokn = md5("{$token}-{$_post['_uri']}");
    }

    // empty the uploaded directory of previous uploads on page refresh.
    $cdir = jrCore_get_module_cache_dir('jrCore');
    if (is_dir("{$cdir}/{$tokn}")) {
        jrCore_delete_dir_contents("{$cdir}/{$tokn}");
        rmdir("{$cdir}/{$tokn}");
    }

    $uniq = substr(md5(microtime()), 0, 8);
    $size = (jrCore_checktype(intval($max_size), 'number_nz')) ? intval($max_size) : 2097152;
    $_js  = array("
    try {
    var active_{$uniq}_uploads = {};
    var active_{$uniq}_ulcount = 0;
    var pm_{$_field['name']} = new qq.FileUploader({
        element: document.getElementById('pm_{$_field['name']}'),
        action: '{$_conf['jrCore_base_url']}/{$curl}/upload_file/',
        inputName: 'pm_{$_field['name']}',
        acceptFiles: '{$allowed}',
        sizeLimit: {$size},
        multiple: " . $multi . ",
        debug: " . $debug . ",
        params: { upload_name: '{$_field['name']}', field_name: 'pm_{$_field['name']}', token: '{$token}', upload_token: '{$tokn}', extensions: '{$allowed}', multiple: '{$multi}' },
        uploadButtonText: '" . addslashes($text) . "',
        cancelButtonText: '" . addslashes($_lang['jrCore'][2]) . "',
        failUploadText: '" . addslashes($_lang['jrCore'][44]) . "',
        onSubmit: function(id,name) {
            active_{$uniq}_ulcount++;
            if ({$maxup} > 0 && active_{$uniq}_ulcount > {$maxup}) { return false; }
        },
        onUpload: function(id,name) {
            active_{$uniq}_uploads[name] = 1;
            $('.form_submit_section input').attr(\"disabled\",\"disabled\").addClass('form_button_disabled');
        },
        onComplete: function(id,name,res) {
            delete active_{$uniq}_uploads[name]; var count = 0;
            for (i in active_{$uniq}_uploads) { if (active_{$uniq}_uploads.hasOwnProperty(i)) { count++; } }
            if (count === 0) { $('.form_submit_section input').removeAttr(\"disabled\",\"disabled\").removeClass('form_button_disabled'); }
        }
    });
    } catch(e) {}");
    jrCore_create_page_element('javascript_ready_function', $_js);

    // Add in our unique upload directory token if needed
    if (!jrCore_get_flag('jrcore_upload_token_added')) {
        $_tmp = array(
            'type'  => 'hidden',
            'name'  => 'upload_token',
            'value' => $tokn
        );
        jrCore_form_field_create($_tmp);
        jrCore_set_flag('jrcore_upload_token_added', 1);
    }

    // Rewrite HTML...
    $idx = jrCore_form_field_get_tab_index($_field);
    if (!isset($_field['html'])) {
        $_field['html'] = '';
    }
    $_field['html'] = $_field['html'] . '<div id="pm_' . $_field['name'] . '" class="qq-upload-holder" tabindex="' . $idx . '"><noscript><p>Please enable JavaScript to use file uploader.</p></noscript></div>';

    $add = '';
    if ($multi !== false && $multi !== 'false') {
        $add = ' ' . $_lang['jrCore'][69];
    }
    if (!isset($_field['sublabel']) || strlen(trim($_field['sublabel'])) === 0) {
        $_field['sublabel'] = $_lang['jrCore'][49] . ' ' . jrCore_format_size($size) . $add;
    }
    else {
        $_field['sublabel'] .= '<br>' . $_lang['jrCore'][49] . ' ' . jrCore_format_size($size) . $add;
    }

    if ($multi && $maxup > 0) {
        $_field['sublabel'] .= "<br><b>{$maxup}&nbsp;{$_lang['jrCore'][82]}</b>";
    }

    if (!isset($_field['help'])) {
        $_field['help'] = '';
    }
    $allowed        = str_replace(',', ', ', $allowed);
    $_field['help'] .= '<br><br>' . $_lang['jrCore'][68] . ' <strong>' . $allowed . '</strong>';
    return $_field;
}

//------------------------------------------------------------------
// FORM elements
//------------------------------------------------------------------

/**
 * Start a new form in a page
 *
 * @param string $form_name Form name/ID
 * @param string $action URL for form action
 * @param array $_att additional form element attributes
 * @return string Returns MD5 string that is the HTML form token
 *
 *   <!ELEMENT FORM - - (%block;|SCRIPT)+ -(FORM) -- interactive form -->
 *   <!ATTLIST FORM
 *     %attrs;                              -- %coreattrs, %i18n, %events --
 *     action      %URI;          #REQUIRED -- server-side form handler --
 *     method      (GET|POST)     GET       -- HTTP method used to submit the form--
 *     enctype     %ContentType;  "application/x-www-form-urlencoded"
 *     accept      %ContentTypes; #IMPLIED  -- list of MIME types for file upload --
 *     name        CDATA          #IMPLIED  -- name of form for scripting --
 *     onsubmit    %Script;       #IMPLIED  -- the form was submitted --
 *     onreset     %Script;       #IMPLIED  -- the form was reset --
 *     accept-charset %Charsets;  #IMPLIED  -- list of supported charsets --
 *     >
 */
function jrCore_form_begin($form_name, $action, $_att = null)
{
    global $_conf, $_urls, $_user;
    // Validate extra attributes
    unset($_att['id'], $_att['name'], $_att['action']);
    // Our defaults
    $_def = array(
        'method'         => array('post', 'get'),
        'accept-charset' => 'utf-8',
        'enctype'        => array('application/x-www-form-urlencoded', 'multipart/form-data')
    );
    $_att = jrCore_form_check_default_attributes($_def, $_att);

    // Create our form element based on what we have
    $html = '<form class="jrform" id="' . $form_name . '" name="' . $form_name . '" action="' . $action . '" method="post" accept-charset="utf-8" enctype="multipart/form-data">' . "\n";
    $_tmp = array(
        'type'      => 'form_begin',
        'action'    => $action,
        'form_name' => $form_name,
        'form_html' => $html,
        'attr'      => $_att,
        'module'    => 'jrCore',
        'template'  => 'form_begin.tpl'
    );
    jrCore_create_page_element('form_begin', $_tmp);

    // Get our CSRF form token inserted into the form as a hidden element
    $tok = jrCore_form_token_create($form_name . $action);
    jrCore_set_flag("jr_html_form_token_{$form_name}", $tok);

    // Form ID
    $html = '<input type="hidden" id="jr_html_form_token" name="jr_html_form_token" value="' . $tok . '">' . "\n";
    $_tmp = array(
        'type'      => 'hidden',
        'form_html' => $html,
        'module'    => 'jrCore'
    );
    jrCore_create_page_element('form_hidden', $_tmp);

    // Active Profile ID
    if (jrUser_is_logged_in()) {
        $apid = $_user['user_active_profile_id'];
        $rurl = jrCore_get_local_referrer();
        if ($rurl && (!isset($_user['profile_url']) || !strpos($rurl, "/{$_user['profile_url']}"))) {
            // Looks like we may NOT be coming from our active profile
            $rurl = explode('/', str_replace("{$_conf['jrCore_base_url']}/", '', $rurl));
            $rurl = reset($rurl);
            if (!isset($_urls[$rurl])) {
                // We have profile name
                $_tmp = jrCore_db_get_item_by_key('jrProfile', 'profile_url', $rurl, true);
                if ($_tmp && is_array($_tmp)) {
                    $apid = (int) $_tmp['_profile_id'];
                }
            }
        }
        $html = '<input type="hidden" id="jr_html_form_profile_id" name="jr_html_form_profile_id" value="' . $apid . '">' . "\n";
        $_tmp = array(
            'type'      => 'hidden',
            'form_html' => $html,
            'module'    => 'jrCore'
        );
        jrCore_create_page_element('form_hidden', $_tmp);
    }

    // Modal Token
    if (isset($_att['modal_token'])) {
        $html = '<input type="hidden" id="jr_html_modal_token" name="jr_html_modal_token" value="' . $_att['modal_token'] . '">' . "\n";
        $_tmp = array(
            'type'      => 'hidden',
            'form_html' => $html,
            'module'    => 'jrCore'
        );
        jrCore_create_page_element('form_hidden', $_tmp);
    }
    return $tok;
}

/**
 * @ignore
 * jrCore_form_field_hidden_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_hidden_display($_field, $_att = null)
{
    if (!isset($_field['value'])) {
        $_field['value'] = '';
    }
    $html = '<input type="hidden" id="' . $_field['name'] . '" name="' . $_field['name'] . '" value="' . $_field['value'] . '">' . "\n";
    $_tmp = array(
        'type'      => 'hidden',
        'form_html' => $html,
        'module'    => 'jrCore'
    );
    jrCore_create_page_element('form_hidden', $_tmp);
    return true;
}

/**
 * @ignore
 * jrCore_form_field_custom_display
 * @param array $_field Array of Field parameters
 * @return bool
 */
function jrCore_form_field_custom_display($_field)
{
    $_field['type']     = 'custom';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * @ignore
 * jrCore_form_field_notice_display
 * @param array $_field Array of Field parameters
 * @return bool
 */
function jrCore_form_field_notice_display($_field)
{
    $type = 'notice';
    switch ($_field['label']) {
        case 'error':
        case 'success':
            $type = $_field['label'];
            break;
    }
    jrCore_page_notice($type, nl2br($_field['options']), false);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_notice_form_designer_options()
{
    return array(
        'options_help'        => 'Enter the type of notice (one of: error, notice, success) in the <b>Label</b> text field, and the text you want to display in the <b>Options</b> textarea.  HTML is allowed.',
        'disable_default'     => true,
        'disable_validation'  => true,
        'disable_min_and_max' => true,
        'disable_required'    => true
    );
}

/**
 * @ignore
 * jrCore_form_field_editor_display
 * @param array $_field field array field information for editor
 * @param array $_att additional HTML attributes
 * @return bool
 */
function jrCore_form_field_editor_display($_field, $_att = null)
{
    global $_conf, $_mods, $_user;
    $tmp = jrCore_get_flag('jrcore_editor_js_included');
    if (!$tmp) {
        $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/tinymce/tinymce.min.js?v={$_mods['jrCore']['module_updated']}");
        jrCore_create_page_element('javascript_href', $_js);
        jrCore_set_flag('jrcore_editor_js_included', 1);
    }
    $_rp = array(
        'field_name' => $_field['name']
    );
    // Initialize fields
    $_rp['theme'] = 'modern';
    if (isset($_field['theme']) && jrCore_checktype($_field['theme'], 'string')) {
        $_rp['theme'] = ($_field['theme'] == 'advanced') ? 'modern' : $_field['theme'];
    }
    else {
        // Needed to prevent down area from showing for a textarea
        $_field['theme'] = 'modern';
    }
    $allowed_tags = explode(',', $_user['quota_jrCore_allowed_tags']);
    foreach ($allowed_tags as $tag) {
        $tag       = trim($tag);
        $_rp[$tag] = true;
    }

    // See what modules are providing
    $_tm = jrCore_get_registered_module_features('jrCore', 'editor_button');
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $mod => $_items) {
            $tag       = strtolower($mod);
            $_rp[$tag] = false;
            // Make sure the user is allowed Quota access
            if (jrCore_module_is_active($mod) && isset($_user["quota_{$mod}_allowed"]) && $_user["quota_{$mod}_allowed"] == 'on') {
                if (is_file(APP_DIR . "/modules/{$mod}/tinymce/plugin.min.js")) {
                    $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/{$mod}/tinymce/plugin.min.js?v=" . $_mods['jrCore']['module_updated']);
                    jrCore_create_page_element('javascript_href', $_js);
                }
                $_rp[$tag] = true;
            }
        }
    }
    $_rp['form_editor_id'] = 'e' . $_field['name'];

    $ini = @jrCore_parse_template('form_editor.tpl', $_rp, 'jrCore');
    $_js = array($ini);
    jrCore_create_page_element('javascript_ready_function', $_js);

    $cls = 'form_textarea form_editor' . jrCore_form_field_get_hilight($_field['name']);
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $add = '';
    if (isset($_field['full_width']) && $_field['full_width'] === true) {
        $add = ' form_editor_full_width';
    }
    $htm = '<div class="form_editor_holder' . $add . '"><textarea cols="72" rows="6" id="e' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '" tabindex="' . $idx . '"';
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $val = '';
    if (isset($_field['value']) && strlen($_field['value']) > 0) {
        $val = $_field['value'];
    }
    elseif (isset($_field['default']) && strlen($_field['default']) > 0) {
        $val = $_field['default'];
    }
    $htm            .= '>' . $val . '</textarea><input type="hidden" id="' . $_field['name'] . '_editor_contents" name="' . $_field['name'] . '_editor_contents" value=""></div>';
    $_field['html'] = $htm;
    $_field['type'] = 'editor';
    if (isset($_field['full_width']) && $_field['full_width'] === true) {
        unset($_field['label'], $_field['help']);
        $_field['template'] = 'page_custom.tpl';
    }
    else {
        $_field['template'] = 'form_field_elements.tpl';
    }
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_editor_form_designer_options()
{
    return array(
        'disable_options' => true
    );
}

/**
 * @ignore
 * jrCore_form_field_editor_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return bool
 */
function jrCore_form_field_editor_is_empty($_field, &$_post)
{
    $name = $_field['name'];
    if (empty($_post["{$name}_editor_contents"])) {
        $_post[$name] = '';
        unset($_post["{$name}_editor_contents"]);
        return true;
    }
    return false;
}

/**
 * @ignore
 * jrCore_form_field_editor_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error Message to use in validation checking
 * @return mixed
 */
function jrCore_form_field_editor_validate($_field, $_post, $e_msg)
{
    global $_user;
    $name = $_field['name'];
    if (isset($_post["{$name}_editor_contents"]) && strlen($_post["{$name}_editor_contents"]) > 0) {
        $_post[$name] = $_post["{$name}_editor_contents"];
        if (jrCore_checktype($_user['profile_quota_id'], 'number_nz')) {
            // If we have an active Quota ID we need to properly strip tags
            if (isset($_user['quota_jrCore_allowed_tags']) && strlen($_user['quota_jrCore_allowed_tags']) > 0) {
                if (strpos(' ' . $_post[$name], '<script')) {
                    // Strip CDATA tags that TinyMCE adds + prevent <br> in script tags
                    $_post[$name] = str_replace(array('// <![CDATA[', '// ]]>'), '', $_post[$name]);
                }
                $_post[$name] = jrCore_strip_html($_post[$name], $_user['quota_jrCore_allowed_tags']);
            }
            else {
                // No tags allowed
                $_post[$name] = jrCore_strip_html($_post[$name]);
            }
        }
        else {
            // If we get a Quota ID of 0, we remove all HTML
            $_post[$name] = jrCore_strip_html($_post[$name]);
        }
        if (!jrCore_checktype($_post[$name], $_field['validate'])) {
            jrCore_set_form_notice('error', $e_msg);
            return false;
        }
        $min = (isset($_field['min'])) ? intval($_field['min']) : false;
        $max = (isset($_field['max'])) ? intval($_field['max']) : false;
        if (!@jrCore_is_valid_min_max_value($_field['validate'], $_post[$name], $min, $max, $e_msg)) {
            // jrCore_set_form_notice() called in jrCore_is_valid_min_max_value()
            return false;
        }
    }
    else {
        // No Content... are we required?
        if ($_field['required']) {
            jrCore_set_form_notice('error', $e_msg);
            return false;
        }
        $_post[$name] = '';
    }
    unset($_post["{$name}_editor_contents"]);
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_text_display
 *
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 *
 * <!ENTITY % InputType
 *   "(TEXT | PASSWORD | CHECKBOX |
 *     RADIO | SUBMIT | RESET |
 *     FILE | HIDDEN | IMAGE | BUTTON)"
 *    >
 * <!-- attribute name required for all but submit and reset -->
 * <!ELEMENT INPUT - O EMPTY              -- form control -->
 * <!ATTLIST INPUT
 *   %attrs;                              -- %coreattrs, %i18n, %events --
 *   type        %InputType;    TEXT      -- what kind of widget is needed --
 *   name        CDATA          #IMPLIED  -- submit as part of form --
 *   value       CDATA          #IMPLIED  -- Specify for radio buttons and checkboxes --
 *   checked     (checked)      #IMPLIED  -- for radio buttons and check boxes --
 *   disabled    (disabled)     #IMPLIED  -- unavailable in this context --
 *   readonly    (readonly)     #IMPLIED  -- for text and passwd --
 *   size        CDATA          #IMPLIED  -- specific to each type of field --
 *   maxlength   NUMBER         #IMPLIED  -- max chars for text fields --
 *   src         %URI;          #IMPLIED  -- for fields with images --
 *   alt         CDATA          #IMPLIED  -- short description --
 *   usemap      %URI;          #IMPLIED  -- use client-side image map --
 *   ismap       (ismap)        #IMPLIED  -- use server-side image map --
 *   tabindex    NUMBER         #IMPLIED  -- position in tabbing order --
 *   accesskey   %Character;    #IMPLIED  -- accessibility key character --
 *   onfocus     %Script;       #IMPLIED  -- the element got the focus --
 *   onblur      %Script;       #IMPLIED  -- the element lost the focus --
 *   onselect    %Script;       #IMPLIED  -- some text was selected --
 *   onchange    %Script;       #IMPLIED  -- the element value was changed --
 *   accept      %ContentTypes; #IMPLIED  -- list of MIME types for file upload --
 *   >
 */
function jrCore_form_field_text_display($_field, $_att = null)
{
    $cls = 'form_text' . jrCore_form_field_get_hilight($_field['name']);
    if (isset($_field['class']{0})) {
        $cls .= ' ' . $_field['class'];
    }
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $val = '';
    if (isset($_field['value']) && strlen($_field['value']) > 0) {
        $val = $_field['value'];
    }
    elseif (isset($_field['default']) && strlen($_field['default']) > 0) {
        $val = $_field['default'];
    }
    $htm = '<input type="text" id="' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '" value="' . $val . '" tabindex="' . $idx . '"';
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm                .= '>';
    $_field['html']     = $htm;
    $_field['type']     = 'text';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_text_form_designer_options()
{
    return array(
        'disable_options' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_text_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress', 'onkeyup', 'style', 'class', 'autocorrect', 'autocapitalize', 'placeholder');
}

/**
 * @ignore
 * jrCore_form_field_password
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_password_display($_field, $_att = null)
{
    $cls = 'form_text' . jrCore_form_field_get_hilight($_field['name']);
    if (isset($_field['class']{0})) {
        $cls = "{$cls} {$_field['class']}";
    }
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $val = '';
    if (isset($_field['value'])) {
        $val = $_field['value'];
    }
    $htm = '<input type="password" id="' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '" value="' . $val . '" tabindex="' . $idx . '"';
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm                .= '>';
    $_field['html']     = $htm;
    $_field['type']     = 'password';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_password_form_designer_options()
{
    return array(
        'disable_options' => true,
        'disable_default' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_password_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress', 'style', 'class');
}

/**
 * @ignore
 * jrCore_form_field_file
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_file_display($_field, $_att = null)
{
    global $_post;
    // Get existing file if we have one - the "value" we get will
    // be the unique id for the file we are loading.
    $htm = '';
    if (!isset($_field['value']) || !is_array($_field['value'])) {
        // If we are doing an update - we need the full item
        $_field['value'] = jrCore_get_flag('jrcore_form_create_values');
    }
    if (isset($_field['value']) && is_array($_field['value'])) {

        $_fl = array();
        if (!isset($_field['multiple']) || (jrCore_checktype($_field['multiple'], 'number_nz') && $_field['multiple'] > 1) || $_field['multiple'] === true) {
            // Get file fields
            foreach ($_field['value'] as $k => $v) {
                if (strpos($k, "{$_field['name']}_") === 0 && strpos($k, '_size') && jrCore_checktype($v, 'number_nz')) {
                    $_fl[] = array(
                        'field'  => str_replace('_size', '', $k),
                        'unique' => (int) $_field['value']["{$_field['name']}_time"]
                    );
                }
            }
        }
        else {
            if (isset($_field['value']['_item_id']) && jrCore_checktype($_field['value']['_item_id'], 'number_nz') && isset($_field['value']["{$_field['name']}_size"]) && jrCore_checktype($_field['value']["{$_field['name']}_size"], 'number_nz')) {
                $_fl[] = array(
                    'field'  => $_field['name'],
                    'unique' => $_field['value']["{$_field['name']}_time"]
                );
            }
        }

        if ($_fl && count($_fl) > 0) {
            $_rep = array('_items' => array());
            foreach ($_fl as $k => $_fld) {
                $nam = $_fld['field'];
                if (isset($_field['value']["{$nam}_size"]) && jrCore_checktype($_field['value']["{$nam}_size"], 'number_nz')) {
                    $_key               = array('name', 'type', 'size', 'time', 'extension');
                    $_rep['_items'][$k] = array(
                        '_item_id'   => $_field['value']['_item_id'],
                        'field_name' => $nam,
                        'module_url' => $_post['module_url']
                    );
                    foreach ($_key as $v) {
                        $_rep['_items'][$k][$v] = (isset($_field['value']["{$nam}_{$v}"])) ? $_field['value']["{$nam}_{$v}"] : '';
                    }
                    $_rep['_items'][$k]['is_image'] = 0;
                    if (jrImage_is_image_file($_field['value']["{$nam}_name"])) {
                        $_rep['_items'][$k]['is_image'] = 1;
                    }
                }
            }
            $htm = jrCore_parse_template('file_update.tpl', $_rep, 'jrCore');
        }
    }

    $_field['html']     = $htm;
    $_field['type']     = 'file';
    $_field['template'] = 'form_field_elements.tpl';

    // We have a file upload - we need to turn on the progress meter if enabled
    $ext = '';
    // Check for form designer - extension will come in as "options" - always first
    if (isset($_field['options']) && strlen($_field['options']) > 1) {
        $ext = trim($_field['options']);
    }
    elseif (isset($_field['extensions']) && strlen($_field['extensions']) > 0) {
        $ext = trim($_field['extensions']);
    }
    elseif (isset($_field['allowed']) && strlen($_field['allowed']) > 0) {
        $ext = trim($_field['allowed']);
    }
    $_field['multiple'] = (isset($_field['multiple'])) ? $_field['multiple'] : false;
    $_field['max']      = (isset($_field['max']) && jrCore_checktype($_field['max'], 'number_nz')) ? $_field['max'] : jrCore_get_max_allowed_upload(false);
    $_field             = jrCore_enable_meter_support($_field, $ext, $_field['max'], $_field['multiple']);

    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_file_form_designer_options()
{
    return array(
        'options_help'        => 'you can enter the allowed file extensions as a comma separated list - i.e. &quot;txt,pdf,doc,xls&quot; - only files of these types will be allowed to be uploaded.',
        'disable_validation'  => true,
        'disable_default'     => true,
        'disable_min_and_max' => true
    );
}

/**
 * Check to be sure validation is on if field is required
 * @param $_field array Array of Field Parameters
 * @param $_post array Posted Data for checking
 * @return array
 */
function jrCore_form_field_file_params($_field, $_post)
{
    if (!isset($_field['validate'])) {
        $_field['validate'] = 'not_empty';
    }
    if (!isset($_field['error_msg'])) {
        $_lang               = jrUser_load_lang_strings();
        $_field['error_msg'] = $_lang['jrCore'][81];
    }
    return $_field;
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_file_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress', 'style', 'class');
}

/**
 * @ignore
 * Additional validation for "file" form fields.  When getting
 * an uploaded file, we need to make sure it has actually been
 * uploaded if required.
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message for validation
 * @return mixed
 */
function jrCore_form_field_file_validate($_field, $_post, $e_msg)
{
    global $_user;
    // Make sure we got a File..
    $tmp = jrCore_is_uploaded_media_file($_field['module'], $_field['name'], $_user['user_active_profile_id']);
    if (!$tmp) {
        if (!$_field['required']) {
            // file does not exist, but is not required
            return $_post;
        }
        jrCore_set_form_notice('error', $e_msg);
        return false;
    }
    // Okay looks good
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_date
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_date_display($_field, $_att = null)
{
    global $_conf, $_mods;
    // Make sure our date picker is included (only once)
    $tmp = jrCore_get_flag('jrcore_datetime_datepicker_included');
    if (!$tmp) {
        $_ls = jrCore_form_field_get_date_language_strings();
        $_js = array('var ' . implode(",", $_ls) . ';');
        jrCore_create_page_element('javascript_embed', $_js);

        $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/js/jquery.datepicker.1.3.0.js?v={$_mods['jrCore']['module_updated']}");
        jrCore_create_page_element('javascript_href', $_js);
        jrCore_set_flag('jrcore_datetime_datepicker_included', 1);
    }

    // Check for display format
    if (isset($_conf['jrCore_date_format']) && $_conf['jrCore_date_format'] == '%d/%m/%y') {
        $_js = 'onChange: function(target,newDate,locale) { var yy = newDate.getUTCFullYear().toString(); var mm = (newDate.getMonth() + 1).toString(); var dd = newDate.getDate().toString(); target.val((dd[1] ? dd : "0" + dd[0]) +"/"+ (mm[1] ? mm : "0" + mm[0]) +"/"+ yy.substring(2)); }';
    }
    elseif (isset($_conf['jrCore_date_format']) && $_conf['jrCore_date_format'] == '%d %b %Y') {
        $_js = 'onChange: function(target,newDate,locale) { var yy = newDate.getUTCFullYear().toString(); var mm = newDate.toLocaleDateString(locale, {month: \'short\'}).replace(\'\.\',\'\'); var dd = newDate.getDate().toString(); target.val((dd[1] ? dd : "0" + dd[0]) +" "+ mm +" "+ yy); }';
    }
    else {
        $_js = 'onChange: function(target,newDate,locale) { var yy = newDate.getUTCFullYear().toString(); var mm = (newDate.getMonth() + 1).toString(); var dd = newDate.getDate().toString(); target.val((mm[1] ? mm : "0" + mm[0]) +"/"+ (dd[1] ? dd : "0" + dd[0]) +"/"+ yy.substring(2)); }';
    }

    $ini = false;
    $val = jrCore_format_time(time(), true);
    if (isset($_field['value']) && jrCore_checktype($_field['value'], 'number_nz')) {
        $val = jrCore_format_time($_field['value'], true);
        $ini = ($_field['value'] * 1000);
    }
    elseif (isset($_field['default']) && jrCore_checktype($_field['default'], 'number_nz')) {
        $val = jrCore_format_time($_field['default'], true);
        $ini = ($_field['value'] * 1000);
    }
    elseif (isset($_field['default']) && ($_field['default'] == 'false' || $_field['default'] === false)) {
        $val = '';
    }
    if ($ini) {
        $_js = array('var s = new Date(' . $ini . '); try { $(\'#' . $_field['name'] . '\').glDatePicker({ startDate: s, selectedDate: s, ' . $_js . ' }); } catch(e) {};');
    }
    else {
        $_js = array('try { $(\'#' . $_field['name'] . '\').glDatePicker({ ' . $_js . ' }); } catch(e) {};');
    }
    jrCore_create_page_element('javascript_ready_function', $_js);

    // Our "value" will come in as an epoch time - we need to
    // format the data and time portion based on the conf

    $cls = 'form_text form_date' . jrCore_form_field_get_hilight($_field['name']);
    if (isset($_field['class']{0})) {
        $cls = "{$cls} {$_field['class']}";
    }
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm = '<input type="text" id="' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '" value="' . $val . '" tabindex="' . $idx . '"';
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm                .= '>';
    $_field['html']     = $htm;
    $_field['type']     = 'date';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_date_form_designer_options()
{
    return array(
        'disable_options' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_date_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress', 'style', 'class');
}

/**
 * @ignore
 * jrCore_form_field_date_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error Message to use in validation
 * @return mixed
 */
function jrCore_form_field_date_validate($_field, $_post, $e_msg)
{
    global $_conf, $_user;
    $name = $_field['name'];
    // For Date we must convert our incoming values to EPOCH time
    // if have changed the default date format to UK we
    // must convert back here so strtotime() gets it right
    if (isset($_post[$name]) && strlen($_post[$name]) > 0) {

        // Check our date format
        $temp = false;
        switch ($_conf['jrCore_date_format']) {
            // Day / Month / Year
            case '%d/%m/%y':
                list($d, $m, $y) = explode('/', $_post[$name]);
                $temp = strtotime("{$m}/{$d}/{$y}");
                break;
            // Day / Month / Year
            // NOTE: Month will be a locale abbr - i.e. "okt" - need to get month number
            case '%d %b %Y':
                list($d, $m, $y) = explode(' ', trim($_post[$name]));
                $lang = (isset($_conf['jrUser_default_language'])) ? $_conf['jrUser_default_language'] : 'en_US';
                if (jrUser_is_logged_in() && isset($_user['user_language']{1})) {
                    $lang = str_replace('-', '_', $_user['user_language']);
                }
                if ($mnum = jrCore_get_english_month_number($m, $lang)) {
                    $temp = strtotime("{$mnum}/{$d}/{$y}");
                }
                break;
            default:
                $temp = strtotime($_post[$name]);
                break;
        }

        if (!$temp) {
            jrCore_set_form_notice('error', $e_msg);
            jrCore_form_field_hilight($name);
            return false;
        }
        if (!@jrCore_is_valid_min_max_value($_field['validate'], $_post[$name], $_field['min'], $_field['max'], $e_msg)) {
            // jrCore_set_form_notice() called in jrCore_is_valid_min_max_value()
            jrCore_form_field_hilight($name);
            return false;
        }
        $_post[$name] = (int) $temp;
    }
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_datetime_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_datetime_display($_field, $_att = null)
{
    global $_conf, $_mods;
    // Make sure our date picker is included (only once)
    $tmp = jrCore_get_flag('jrcore_datetime_datepicker_included');
    if (!$tmp) {
        $_ls = jrCore_form_field_get_date_language_strings();
        $_js = array('var ' . implode(",", $_ls) . ';');
        jrCore_create_page_element('javascript_embed', $_js);

        $_js = array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/js/jquery.datepicker.1.3.0.js?v={$_mods['jrCore']['module_updated']}");
        jrCore_create_page_element('javascript_footer_href', $_js);
        jrCore_set_flag('jrcore_datetime_datepicker_included', 1);
    }

    // Check for display format
    if (isset($_conf['jrCore_date_format']) && $_conf['jrCore_date_format'] == '%d/%m/%y') {
        $_js = 'onChange: function(target,newDate,locale) { var yy = newDate.getUTCFullYear().toString(); var mm = (newDate.getMonth() + 1).toString(); var dd = newDate.getDate().toString(); target.val((dd[1] ? dd : "0" + dd[0]) +"/"+ (mm[1] ? mm : "0" + mm[0]) +"/"+ yy.substring(2)); }';
    }
    elseif (isset($_conf['jrCore_date_format']) && $_conf['jrCore_date_format'] == '%d %b %Y') {
        $_js = 'onChange: function(target,newDate,locale) { var yy = newDate.getUTCFullYear().toString(); var mm = newDate.toLocaleDateString(locale, {month: \'short\'}).replace(\'\.\',\'\'); var dd = newDate.getDate().toString(); target.val((dd[1] ? dd : "0" + dd[0]) +" "+ mm +" "+ yy); }';
    }
    else {
        $_js = 'onChange: function(target,newDate,locale) { var yy = newDate.getUTCFullYear().toString(); var mm = (newDate.getMonth() + 1).toString(); var dd = newDate.getDate().toString(); target.val((mm[1] ? mm : "0" + mm[0]) +"/"+ (dd[1] ? dd : "0" + dd[0]) +"/"+ yy.substring(2)); }';
    }

    $ini = false;
    $sel = true;
    $val = jrCore_format_time(time(), true);
    if (isset($_field['value']) && jrCore_checktype($_field['value'], 'number_nz')) {
        $val = jrCore_format_time($_field['value'], true);
        $ini = ($_field['value'] * 1000);
        $sel = false;
    }
    elseif (isset($_field['default']) && jrCore_checktype($_field['default'], 'number_nz')) {
        $val = jrCore_format_time($_field['default'], true);
        $ini = ($_field['value'] * 1000);
    }
    if ($ini) {
        $_js = array('var s = new Date(' . $ini . '); try { $(\'#' . $_field['name'] . '_date\').glDatePicker({ startDate: s, selectedDate: s, ' . $_js . ' }); } catch(e) {};');
    }
    else {
        $_js = array('try { $(\'#' . $_field['name'] . '_date\').glDatePicker({ ' . $_js . ' }); } catch(e) {};');
    }
    jrCore_create_page_element('javascript_ready_function', $_js);

    // Our "value" will come in as an epoch time - we need to
    // format the data and time portion based on the conf
    $cls = 'form_date' . jrCore_form_field_get_hilight($_field['name']);
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm = '<input type="text" id="' . $_field['name'] . '_date" class="' . $cls . '" name="' . $_field['name'] . '_date" value="' . ucfirst($val) . '" tabindex="' . $idx . '"';
    if (is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm .= '>';
    // Create our hour picker
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm .= '<select id="' . $_field['name'] . '_time" class="form_select form_time" name="' . $_field['name'] . '_time" tabindex="' . $idx . '">';

    $_hr = array();
    // create our 24 hour time stamps
    $date_s = mktime(0, 0, 0);
    $date_e = ($date_s + 86400);
    $format = $_conf['jrCore_hour_format'];
    if ($_conf['jrCore_hour_format'] == '%I:%M:%S%p') {
        $format = '%I:%M%p';
    }

    if (isset($_field['value']) && jrCore_checktype($_field['value'], 'number_nz')) {
        $val = jrCore_format_time($_field['value'], false, $format);
        $now = $date_s + ($_field['value'] % 86400);
    }
    else {
        $val = jrCore_format_time(time(), false, $format);
        $now = time();
    }

    $add = true;
    while ($date_s < $date_e) {
        if ($sel) {
            if ($date_s > $now && $add) {
                $h       = strftime($format, $now);
                $_hr[$h] = $h;
                $add     = false;
                $sel     = $h;
            }
        }
        else {
            if ($date_s > $now && $add && !isset($_hr[$val])) {
                $_hr[$val] = $val;
            }
        }
        $h       = strftime($format, $date_s);
        $_hr[$h] = $h;
        $date_s  += 900;
    }

    foreach ($_hr as $k => $v) {
        if ($val && $val == $k || $sel && $sel == $k) {
            $htm .= '<option value="' . $k . '" selected> ' . $v . '</option>' . "\n";
        }
        else {
            $htm .= '<option value="' . $k . '"> ' . $v . '</option>' . "\n";
        }
    }
    $htm                .= '</select>';
    $_field['html']     = $htm;
    $_field['type']     = 'datetime';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_datetime_form_designer_options()
{
    return array(
        'disable_options' => true
    );
}

/**
 * @ignore
 * jrCore_form_field_datetime_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return bool
 */
function jrCore_form_field_datetime_is_empty($_field, $_post)
{
    $name = $_field['name'];
    if (empty($_post["{$name}_date"]) && empty($_post["{$name}_time"])) {
        return true;
    }
    return false;
}

/**
 * @ignore
 * jrCore_form_field_datetime_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message to use in validation
 * @return mixed
 */
function jrCore_form_field_datetime_validate($_field, $_post, $e_msg)
{
    global $_user, $_conf;
    $name = $_field['name'];
    if (!isset($_post["{$name}_date"]) || strlen($_post["{$name}_date"]) === 0) {
        jrCore_set_form_notice('error', $e_msg);
        jrCore_form_field_hilight("{$name}_date");
        return false;
    }
    if (!isset($_post["{$name}_time"]) || strlen($_post["{$name}_time"]) === 0) {
        jrCore_set_form_notice('error', $e_msg);
        jrCore_form_field_hilight("{$name}_date");
        return false;
    }
    // Check our date format
    $temp = false;
    switch ($_conf['jrCore_date_format']) {
        // Day / Month / Year
        case '%d/%m/%y':
            list($d, $m, $y) = explode('/', $_post["{$name}_date"]);
            $temp = strtotime("{$m}/{$d}/{$y} " . $_post["{$name}_time"]);
            break;
        // Day / Month / Year
        // NOTE: Month will be a locale abbr - i.e. "okt" - need to get month number
        case '%d %b %Y':
            list($d, $m, $y) = explode(' ', trim($_post["{$name}_date"]));
            $lang = (isset($_conf['jrUser_default_language'])) ? $_conf['jrUser_default_language'] : 'en_US';
            if (jrUser_is_logged_in() && isset($_user['user_language']{1})) {
                $lang = str_replace('-', '_', $_user['user_language']);
            }
            if ($mnum = jrCore_get_english_month_number($m, $lang)) {
                $temp = strtotime("{$mnum}/{$d}/{$y} " . $_post["{$name}_time"]);
            }
            break;
        default:
            $temp = strtotime($_post["{$name}_date"] . ' ' . $_post["{$name}_time"]);
            break;
    }
    if (!$temp) {
        jrCore_set_form_notice('error', $e_msg);
        jrCore_form_field_hilight("{$name}_date");
        return false;
    }
    if (!@jrCore_is_valid_min_max_value($_field['validate'], $_post[$name], $_field['min'], $_field['max'], $e_msg)) {
        jrCore_form_field_hilight($name);
        return false;
    }
    $_post[$name] = (int) $temp;
    unset($_post["{$name}_date"]);
    unset($_post["{$name}_time"]);
    return $_post;
}

/**
 * Get an english month number from a foreign month name
 * @param string $name
 * @param string $locale
 * @return mixed
 */
function jrCore_get_english_month_number($name, $locale)
{
    setlocale(LC_TIME, 'en_US.UTF-8');
    $month_numbers = range(1, 12);
    $_english_full = array();
    $_english_abbr = array();
    foreach ($month_numbers as $month) {
        $_english_full[$month] = jrCore_str_to_lower(strftime('%B', mktime(0, 0, 0, $month, 1, 2020)));
        $_english_abbr[$month] = jrCore_str_to_lower(strftime('%b', mktime(0, 0, 0, $month, 1, 2020)));
    }
    setlocale(LC_ALL, $locale . '.UTF-8');
    foreach ($month_numbers as $month) {
        $name = jrCore_str_to_lower($name);
        $tmp  = jrCore_str_to_lower(strftime('%b', mktime(0, 0, 0, $month, 1, 2020)));
        if ($tmp == $name) {
            // We've found our month
            if (isset($_english_abbr[$month])) {
                return str_pad($month, 2, '0', STR_PAD_LEFT);
            }
        }
        $tmp = jrCore_str_to_lower(strftime('%B', mktime(0, 0, 0, $month, 1, 2020)));
        if ($tmp == $name) {
            // We've found our month
            if (isset($_english_full[$month])) {
                return str_pad($month, 2, '0', STR_PAD_LEFT);
            }
        }

    }
    return false;
}

/**
 * Get language strings for use in the date selector
 * @return array
 */
function jrCore_form_field_get_date_language_strings()
{
    global $_conf, $_user;
    $lang = (isset($_conf['jrUser_default_language'])) ? $_conf['jrUser_default_language'] : 'en_US';
    if (jrUser_is_logged_in() && isset($_user['user_language']{1})) {
        $lang = $_user['user_language'];
    }
    // Lang strings
    $_ln = jrUser_load_lang_strings();
    $_ls = array(
        '_dp_jan' => $_ln['jrCore'][10],
        '_dp_feb' => $_ln['jrCore'][11],
        '_dp_mar' => $_ln['jrCore'][12],
        '_dp_apr' => $_ln['jrCore'][13],
        '_dp_may' => $_ln['jrCore'][14],
        '_dp_jun' => $_ln['jrCore'][15],
        '_dp_jul' => $_ln['jrCore'][16],
        '_dp_aug' => $_ln['jrCore'][17],
        '_dp_sep' => $_ln['jrCore'][18],
        '_dp_oct' => $_ln['jrCore'][19],
        '_dp_nov' => $_ln['jrCore'][20],
        '_dp_dec' => $_ln['jrCore'][21],
        '_dp_sun' => $_ln['jrCore'][136],
        '_dp_mon' => $_ln['jrCore'][137],
        '_dp_tue' => $_ln['jrCore'][138],
        '_dp_wed' => $_ln['jrCore'][139],
        '_dp_thu' => $_ln['jrCore'][140],
        '_dp_fri' => $_ln['jrCore'][141],
        '_dp_sat' => $_ln['jrCore'][142],
        '_dp_lng' => $lang
    );
    foreach ($_ls as $k => $v) {
        $_ls[$k] = "{$k} = '" . jrCore_entity_string($v) . "'";
    }
    return $_ls;
}

/**
 * @ignore
 * jrCore_form_field_select_date_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_select_date_display($_field, $_att = null)
{
    // Bring in language
    $_lang = jrUser_load_lang_strings();

    // Our "value" will come in as an epoch time - we need to
    // format the data and time portion based on the conf

    // Year / Month / Day
    $y_v = substr($_field['value'], 0, 4);
    $m_v = substr($_field['value'], 4, 2);
    $d_v = substr($_field['value'], 6, 2);

    $cls = 'form_year' . jrCore_form_field_get_hilight($_field['name']);
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm = '<select id="' . $_field['name'] . '_year" class="form_select ' . $cls . '" name="' . $_field['name'] . '_year" tabindex="' . $idx . '">';
    $yer = 1940;
    $now = strftime('%Y');
    while ($yer <= $now) {
        if (isset($y_v) && $y_v == "{$yer}") {
            $htm .= '<option value="' . $yer . '" selected> ' . $yer . '</option>' . "\n";
        }
        else {
            $htm .= '<option value="' . $yer . '"> ' . $yer . '</option>' . "\n";
        }
        $yer++;
    }
    $htm .= '</select>';
    $htm .= '<select id="' . $_field['name'] . '_month" class="form_select form_month" name="' . $_field['name'] . '_month" tabindex="' . $idx . '">';
    $_mn = array(
        '01' => $_lang['jrCore'][10],
        '02' => $_lang['jrCore'][11],
        '03' => $_lang['jrCore'][12],
        '04' => $_lang['jrCore'][13],
        '05' => $_lang['jrCore'][14],
        '06' => $_lang['jrCore'][15],
        '07' => $_lang['jrCore'][16],
        '08' => $_lang['jrCore'][17],
        '09' => $_lang['jrCore'][18],
        '10' => $_lang['jrCore'][19],
        '11' => $_lang['jrCore'][20],
        '12' => $_lang['jrCore'][21]
    );
    foreach ($_mn as $num => $desc) {
        if (isset($m_v) && $m_v == "{$num}") {
            $htm .= '<option value="' . $num . '" selected> ' . $desc . '</option>' . "\n";
        }
        else {
            $htm .= '<option value="' . $num . '"> ' . $desc . '</option>' . "\n";
        }
        $yer++;
    }
    $htm .= '</select>';
    $htm .= '<select id="' . $_field['name'] . '_day" class="form_select form_day" name="' . $_field['name'] . '_day" tabindex="' . $idx . '">';
    $day = 1;
    while ($day < 32) {
        $day = str_pad($day, 2, '0', STR_PAD_LEFT);
        if (isset($d_v) && $d_v == "{$day}") {
            $htm .= '<option value="' . $day . '" selected> ' . $day . '</option>' . "\n";
        }
        else {
            $htm .= '<option value="' . $day . '"> ' . $day . '</option>' . "\n";
        }
        $yer++;
    }
    $htm                .= '</select>';
    $_field['html']     = $htm;
    $_field['type']     = 'selectdate';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * @ignore
 * jrCore_form_field_select_and_text_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_select_and_text_display($_field, $_att = null)
{
    global $_post, $_user;
    // In our "select_and_text" field, we have 2 elements:
    // A select field that is unique by EXISTING entries in the DataStore for the given field
    // A Text Entry box to enter a NEW value - the module MUST be using a
    // DataStore in order for this to be supported.

    // See if we have predefined options
    if (isset($_field['options']{0})) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }

    if (jrCore_db_get_prefix($_post['module']) && isset($_field['name']) && (!isset($_field['options']) || !is_array($_field['options']))) {
        // Go get our options
        $_sc = array(
            'search'         => array(
                "_profile_id = {$_user['user_active_profile_id']}",
                "{$_field['name']} like %"
            ),
            'group_by'       => "{$_field['name']}",
            'order_by'       => array(
                "{$_field['name']}" => 'ASC'
            ),
            'limit'          => 250,
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'ignore_pending' => true
        );
        $_rt = jrCore_db_search_items($_post['module'], $_sc);
        if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
            $_field['options'] = array();
            foreach ($_rt['_items'] as $k => $_v) {
                if (!empty($_v["{$_field['name']}"])) {
                    $val                     = trim($_v["{$_field['name']}"]);
                    $_field['options'][$val] = $val;
                }
                unset($_rt['_items'][$k]);
            }
        }
    }
    if (isset($_field['options']) && is_array($_field['options']) && count($_field['options']) > 0) {

        // Bring in lang strings
        $_lang = jrUser_load_lang_strings();

        $cls = 'form_select_and_text_select' . jrCore_form_field_get_hilight("{$_field['name']}_select");
        $idx = jrCore_form_field_get_tab_index($_field);
        $htm = "\n" . '<select id="' . $_field['name'] . '_select" class="form_select ' . $cls . '" name="' . $_field['name'] . '_select" tabindex="' . $idx . '"';
        if (isset($_att) && is_array($_att)) {
            foreach ($_att as $key => $attr) {
                $htm .= ' ' . $key . '="' . $attr . '"';
            }
        }
        $htm .= ">\n";
        foreach ($_field['options'] as $k => $val) {
            if (strlen($k) === 0) {
                continue;
            }
            $k = jrCore_entity_string($k);
            if (isset($_field['value']) && ($_field['value'] == $k || trim($_field['value']) == $val)) {
                $htm .= '<option value="' . $k . '" selected> ' . $val . '</option>' . "\n";
            }
            else {
                $htm .= '<option value="' . $k . '"> ' . $val . '</option>' . "\n";
            }
        }
        $htm .= '</select>';
        unset($_rt, $_opts);

        // Now add in our text field for the new value
        $cls = 'form_select_and_text_text' . jrCore_form_field_get_hilight("{$_field['name']}_text");
        $idx = jrCore_form_field_get_tab_index($_field);
        $htm .= '<span class="subtitle form_select_and_text_tag">' . $_lang['jrCore'][48] . '</span><input type="text" id="' . $_field['name'] . '_text" class="form_text ' . $cls . '" name="' . $_field['name'] . '_text" value="" tabindex="' . $idx . '">';

        $_field['html']     = $htm;
        $_field['type']     = 'select_and_text';
        $_field['template'] = 'form_field_elements.tpl';
    }
    else {
        // We have no existing values - use a normal text field
        $cls = 'form_text' . jrCore_form_field_get_hilight($_field['name']);
        // Get our tab index
        $idx = jrCore_form_field_get_tab_index($_field);
        $val = '';
        if (isset($_field['value'])) {
            $val = $_field['value'];
        }
        $htm = '<input type="text" id="' . $_field['name'] . '_text" class="' . $cls . '" name="' . $_field['name'] . '_text" value="' . $val . '" tabindex="' . $idx . '"';
        if (isset($_att) && is_array($_att)) {
            foreach ($_att as $key => $attr) {
                $htm .= ' ' . $key . '="' . $attr . '"';
            }
        }
        $htm                .= '>';
        $_field['html']     = $htm;
        $_field['type']     = 'text';
        $_field['template'] = 'form_field_elements.tpl';
        $_field['type']     = 'text';
    }
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_select_and_text_form_designer_options()
{
    return array(
        'disable_min_and_max' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_select_and_text_attributes()
{
    return array('disabled', 'onfocus', 'onblur', 'onchange', 'style');
}

/**
 * @ignore
 * jrCore_form_field_select_and_text_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return bool
 */
function jrCore_form_field_select_and_text_is_empty($_field, $_post)
{
    $name = $_field['name'];
    if (empty($_post["{$name}_select"]) && empty($_post["{$name}_text"])) {
        return true;
    }
    return false;
}

/**
 * @ignore
 * jrCore_form_field_select_and_text_assembly
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return array
 */
function jrCore_form_field_select_and_text_assembly($_field, $_post)
{
    $name = $_field['name'];
    if (empty($_post["{$name}_text"]) && !empty($_post["{$name}_select"])) {
        $_post[$name] = $_post["{$name}_select"];
    }
    elseif (!empty($_post["{$name}_text"])) {
        $_post[$name] = $_post["{$name}_text"];
    }
    else {
        $_post[$name] = '';
    }
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_select_and_text_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message to use in validation
 * @return mixed
 */
function jrCore_form_field_select_and_text_validate($_field, $_post, $e_msg)
{
    $name = $_field['name'];
    if (isset($_post["{$name}_text"]) && strlen($_post["{$name}_text"]) > 0) {
        $_post[$name] = $_post["{$name}_text"];
    }
    elseif (isset($_post["{$name}_select"]) && strlen($_post["{$name}_select"]) > 0) {
        $_post[$name] = $_post["{$name}_select"];
    }
    if (!jrCore_checktype($_post[$name], $_field['validate'])) {
        jrCore_set_form_notice('error', $e_msg);
        jrCore_form_field_hilight("{$name}_select");
        jrCore_form_field_hilight("{$name}_text");
        return false;
    }
    if (!@jrCore_is_valid_min_max_value($_field['validate'], $_post[$name], $_field['min'], $_field['max'], $e_msg)) {
        // jrCore_set_form_notice() called in jrCore_is_valid_min_max_value()
        jrCore_form_field_hilight("{$name}_select");
        jrCore_form_field_hilight("{$name}_text");
        return false;
    }
    unset($_post["{$name}_select"]);
    unset($_post["{$name}_text"]);
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_live_search_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_live_search_display($_field, $_att = null)
{
    global $_conf, $_post;
    // We must have a live search URL
    if (!isset($_field['target']) || !jrCore_checktype($_field['target'], 'url')) {
        $_field['target'] = "{$_conf['jrCore_base_url']}/" . jrCore_get_module_url('jrCore') . "/live_search_options/m={$_post['module']}/o={$_post['option']}/n={$_field['name']}";
    }
    $delay = 400;
    if (isset($_att['typedelay']) && jrCore_checktype($_att['typedelay'], 'number_nz')) {
        $delay = (int) $_att['typedelay'];
    }
    $_tmp = array("try { $('#{$_field['name']}').liveSearch({url:'{$_field['target']}/q=', typeDelay: {$delay}}); } catch(e) {};");
    jrCore_create_page_element('javascript_ready_function', $_tmp);

    // For a live search we can get a different KEY and VALUE (since the key or value may
    // not be available until a live search is actually run
    $key = '';
    $val = '';
    if (isset($_field['value']) && is_array($_field['value'])) {
        // value => ('key' => 'value')
        $val = reset($_field['value']);
        $key = array_keys($_field['value']);
        $key = reset($key);
    }
    elseif (isset($_field['value']) && strlen($_field['value']) > 0) {
        $val = $_field['value'];
    }
    elseif (isset($_field['default']) && is_array($_field['default'])) {
        // value => ('key' => 'value')
        $val = reset($_field['default']);
        $key = array_keys($_field['default']);
        $key = reset($key);
    }
    elseif (isset($_field['default']) && strlen($_field['default']) > 0) {
        $val = $_field['default'];
    }

    // We also need to create our value holder for the actual
    // value that gets selected in the search box
    $_tmp = array(
        'type'  => 'hidden',
        'name'  => "{$_field['name']}_livesearch_value",
        'value' => (strlen($key) > 0) ? $key : $val
    );
    jrCore_form_field_create($_tmp);

    // Our live search field is an enhanced text field
    $_lang = jrUser_load_lang_strings();
    if (strlen($val) === 0) {
        if (!isset($_field['value']) || $_field['value'] !== false) {
            $_field['value'] = $_lang['jrCore'][8];
            if (isset($_field['class']{0})) {
                $_field['class'] = "{$_field['class']} live_search_text";
            }
            else {
                $_field['class'] = 'live_search_text';
            }
        }
    }
    else {
        $_field['value'] = $val;
    }

    // Extra attributes
    if (!is_array($_att)) {
        $_att = array();
    }
    // Get any extra attributes for the text field into our $_att array
    $_tf = jrCore_form_field_text_attributes();
    if ($_tf && is_array($_tf)) {
        foreach ($_tf as $f) {
            if (isset($_field[$f])) {
                $_att[$f] = $_field[$f];
            }
        }
    }
    if (!isset($_att['placeholder'])) {
        $_att['onfocus'] = "if($(this).val() == '" . jrCore_entity_string($_lang['jrCore'][8]) . "'){ $(this).val('').removeClass('live_search_text'); }";
    }

    return jrCore_form_field_text_display($_field, $_att);
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_live_search_form_designer_options()
{
    return array(
        'options_help'        => 'you can enter the searchable options ONE PER LINE, in the following format: <strong>Option Value|Option Text</strong> - you may also enter a valid module FUNCTION name that will return the options dynamically.',
        'disable_default'     => true,
        'disable_validation'  => true,
        'disable_min_and_max' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_live_search_attributes()
{
    return array('disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onkeypress', 'style', 'class', 'typedelay');
}

/**
 * @ignore
 * jrCore_form_field_live_search_assembly
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return array
 */
function jrCore_form_field_live_search_assembly($_field, $_post)
{
    global $_user;
    $name = $_field['name'];
    $ukey = (isset($_user['_user_id'])) ? $_user['_user_id'] : 0;
    // If our field comes in empty, it means the user selected a value,
    // but then changed their mind and erased the value they had entered
    if (!isset($_post[$name]) || strlen($_post[$name]) === 0) {
        unset($_post["{$name}_livesearch_value"]);
    }
    elseif (isset($_post["{$name}_livesearch_value"]) && strlen($_post["{$name}_livesearch_value"]) > 0 && isset($_post[$name]) && $_post[$name] != 'search' && $_post[$name] != $_post["{$name}_livesearch_value"]) {
        // Make sure the $_post[$name] matches our validation
        if (isset($_field['validate']) && !jrCore_checktype($_post[$name], $_field['validate'])) {
            $_post[$name] = $_post["{$name}_livesearch_value"];
        }
        unset($_post["{$name}_livesearch_value"]);
    }
    elseif (isset($_post["{$name}_livesearch_value"]) && strlen($_post["{$name}_livesearch_value"]) > 0 && $_post["{$name}_livesearch_value"] != 'search') {
        $_post[$name] = $_post["{$name}_livesearch_value"];
        unset($_post["{$name}_livesearch_value"]);
    }
    elseif (isset($_post[$name]) && strlen($_post[$name]) > 0) {
        // It could be that the user simply typed in the entire string
        // that was searched on, and by not clicking on the live search
        // results, the hidden ID will not have been updated.
        $_tmp = jrCore_get_temp_value('jrCore', "{$ukey}_{$name}_live_search");
        if ($_tmp) {
            foreach ($_tmp as $k => $v) {
                if ($_post[$name] == $v) {
                    $_post[$name] = $k;
                    break;
                }
            }
        }
    }
    jrCore_delete_temp_value('jrCore', "{$ukey}_{$name}_live_search");
    return $_post;
}

/**
 * Format and return a result for a live_query search
 * @param $field string Field Name to put result in
 * @param $_data array of data for result
 * @return string
 */
function jrCore_live_search_results($field, $_data)
{
    global $_user;
    if (is_array($_data) && !empty($_data)) {
        jrCore_set_temp_value('jrCore', "{$_user['_user_id']}_{$field}_live_search", $_data);
        $_out = array();
        foreach ($_data as $k => $v) {
            $_out[] = "<a onclick=\"$('#{$field}_livesearch_value').val('" . addslashes($k) . "');$('#{$field}').val('" . addslashes($v) . "');$('#jquery-live-search').slideUp(200);return false\">{$v}</a>";
        }
        return implode('<br>', $_out);
    }
    return 'no results';
}

/**
 * @ignore
 * jrCore_form_field_checkbox_spambot_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_checkbox_spambot_display($_field, $_att = null)
{
    // We can only have 1 spam bot checkbox per form
    $tmp = jrCore_get_flag('jrcore_form_field_checkbox_spambot');
    if ($tmp) {
        return true;
    }

    // Make sure our name is randomized so it will always be different
    $_field['name'] = $_field['name'] . mt_rand(1000000, 9999999);

    // This flag is checked in jrCore_form_submit - this will let the
    // the actual field be removed from the HTML and setup in JS instead.
    jrCore_set_flag('jrcore_form_field_checkbox_spambot', $_field['name']);

    $_field['html']     = '<span id="sb_' . $_field['name'] . '"></span>'; // HTML form element is added via Javascript
    $_field['type']     = 'checkbox_spambot';
    $_field['template'] = 'form_field_elements.tpl';
    $_field['required'] = 'on';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_checkbox_spambot_form_designer_options()
{
    return array(
        'disable_validation'  => true,
        'disable_options'     => true,
        'disable_min_and_max' => true
    );
}

/**
 * @ignore
 * jrCore_form_field_checkbox_spambot_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message for validation
 * @return mixed
 */
function jrCore_form_field_checkbox_spambot_validate($_field, $_post, $e_msg)
{
    // Make sure we get our field
    foreach ($_post as $k => $v) {
        if (strpos($k, $_field['name']) === 0 && $v == 'on') {
            return $_post;
        }
    }
    jrCore_set_form_notice('error', $e_msg);
    return false;
}

/**
 * @ignore
 * jrCore_form_field_checkbox_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_checkbox_display($_field, $_att = null)
{
    // A checkbox field will NOT be present in $_post if it is
    // unchecked, so we create a HIDDEN form field before the
    // checkbox with value set to "off"
    // DO NOT USE jrCore_form_field_create here - it will break things!
    $html = '<input type="hidden" name="' . $_field['name'] . '" value="off">' . "\n";
    $_tmp = array(
        'type'      => 'hidden',
        'form_html' => $html,
        'module'    => 'jrCore'
    );
    jrCore_create_page_element('form_hidden', $_tmp);

    $checked = '';
    if (isset($_field['value']) && $_field['value'] == 'on') {
        $checked = ' checked="checked"';
    }
    elseif (!isset($_field['value']) && isset($_field['default']) && jrCore_checktype($_field['default'], 'onoff') && $_field['default'] == 'on') {
        $checked = ' checked="checked"';
    }
    $cls = 'form_checkbox' . jrCore_form_field_get_hilight($_field['name']);
    $beg = '';
    $end = '';
    if (strlen($cls) > 14) {
        // We are in error - we need to highlight the area around the checkbox so the user can see
        $beg = '<span class="field-hilight" style="padding:6px 3px;">';
        $end = '</span>';
    }
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm = $beg . '<input type="checkbox" id="' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '" tabindex="' . $idx . '"' . $checked;
    if ($_att && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm                .= '>' . $end;
    $_field['html']     = $htm;
    $_field['type']     = 'checkbox';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_checkbox_form_designer_options()
{
    return array(
        'disable_validation'  => true,
        'disable_options'     => true,
        'disable_min_and_max' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_checkbox_attributes()
{
    return array('disabled');
}

/**
 * @ignore
 * jrCore_form_field_checkbox_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message for validation
 * @return mixed
 */
function jrCore_form_field_checkbox_validate($_field, $_post, $e_msg)
{
    if (!isset($_post["{$_field['name']}"])) {
        $_post["{$_field['name']}"] = 'off';
    }
    if (!jrCore_checktype($_post["{$_field['name']}"], 'onoff')) {
        jrCore_set_form_notice('error', $e_msg);
        return false;
    }
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_radio
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_radio_display($_field, $_att = null)
{
    $cls = 'form_radio' . jrCore_form_field_get_hilight($_field['name']);
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm = '';
    if (isset($_field['options']{0})) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }
    if (isset($_field['options']) && is_array($_field['options'])) {
        foreach ($_field['options'] as $k => $v) {
            $checked = '';
            if (isset($_field['value']) && $_field['value'] == $k) {
                $checked = 'checked="checked"';
            }
            elseif (!isset($_field['value']) && isset($_field['default']) && $_field['default'] == $k) {
                $checked = 'checked="checked"';
            }
            $htm .= '<div class="form_radio_option"><input type="radio" class="' . $cls . '" name="' . $_field['name'] . '" value="' . addslashes($k) . '" tabindex="' . $idx . '" ' . $checked;
            if (isset($_att) && is_array($_att)) {
                foreach ($_att as $key => $attr) {
                    $htm .= ' ' . $key . '="' . $attr . '"';
                }
            }
            if (isset($_field['style'])) {
                $htm .= '>&nbsp;<span style="' . $_field['style'] . '">' . $v . '</span></div>';
            }
            else {
                $htm .= '>&nbsp;' . $v . '</div>';
            }
            if (isset($_field['layout']) && $_field['layout'] == 'vertical') {
                $htm .= '<br>';
            }
            $htm .= "\n";
        }
    }
    $_field['html']     = $htm;
    $_field['type']     = 'radio';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_radio_form_designer_options()
{
    return array(
        'options_help'       => 'you can enter available options ONE PER LINE, in the following format: <strong>Option Value|Option Text</strong> - you may also enter a valid module FUNCTION name that will return the options dynamically.',
        'disable_validation' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_radio_attributes()
{
    return array('onclick', 'class', 'style');
}

/**
 * @ignore
 * jrCore_form_field_radio_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message for form validation
 * @return mixed
 */
function jrCore_form_field_radio_validate($_field, $_post, $e_msg)
{
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }
    if (!isset($_field['options']) || !is_array($_field['options'])) {
        jrCore_set_form_notice('error', "invalid options received for field: {$_field['label']}");
        jrCore_form_result();
    }
    $name = $_post["{$_field['name']}"];
    // Our value must be in the option list
    if (!isset($_field['options'][$name])) {
        jrCore_set_form_notice('error', $e_msg);
        jrCore_form_field_hilight($_field['name']);
        return false;
    }
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_optionlist_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_optionlist_display($_field, $_att = null)
{
    global $_post;
    $_lang = jrUser_load_lang_strings();

    if (isset($_field['options']{0})) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }
    if (isset($_field['value']) && !is_array($_field['value']) && strlen($_field['value']) > 0) {
        $_field['value'] = explode(',', $_field['value']);
    }
    if (isset($_field['default']) && !is_array($_field['default']) && strlen($_field['default']) > 0) {
        $_field['default'] = explode(',', $_field['default']);
    }

    // A checkbox field will NOT be present in $_post if it is
    // unchecked, so we create a HIDDEN form field before the
    // checkbox with value set to "off"
    // DO NOT USE jrCore_form_field_create here - it will break things!
    $html = '<input type="hidden" name="' . $_field['name'] . '" value="off">' . "\n";
    $_tmp = array(
        'type'      => 'hidden',
        'form_html' => $html,
        'module'    => 'jrCore'
    );
    jrCore_create_page_element('form_hidden', $_tmp);

    // Setup our values
    $_cb = array();
    foreach ($_field['options'] as $fname => $display) {
        $mod = $_post['module'];
        if (jrCore_checktype($display, 'number_nz') && isset($_lang[$mod][$display])) {
            $display = $_lang[$mod][$display];
        }
        $idx = jrCore_form_field_get_tab_index($_field);
        $chk = '';
        if (isset($_field['value']) && is_array($_field['value']) && in_array($fname, $_field['value'])) {
            $chk = ' checked="checked"';
        }
        elseif ((!isset($_field['value']) || !is_array($_field['value'])) && (isset($_field['default']) && is_array($_field['default']) && in_array($fname, $_field['default']))) {
            $chk = ' checked="checked"';
        }
        $dcs   = 'form_option_list_text';
        $_cb[] = '<input type="checkbox" id="' . $_field['name'] . '_' . $fname . '" data-key="' . $fname . '" class="form_checkbox" tabindex="' . $idx . '" name="' . $_field['name'] . '_' . $fname . '" ' . $chk . '>&nbsp<span class="' . $dcs . '">' . $display . "</span>\n";
    }
    // Layout
    if (isset($_field['layout']) && $_field['layout'] == 'horizontal') {
        $_field['html'] = '<div style="display:inline-block;">' . implode('&nbsp', $_cb) . '</div>';
    }
    elseif (isset($_field['layout']) && $_field['layout'] == 'columns') {
        $_field['html'] = '<div style="display:table;width:90%;float:left"><div style="display:table-row">';
        if (!isset($_field['columns']) || !jrCore_checktype($_field['columns'], 'number_nz')) {
            $_field['columns'] = 2;
        }
        $cnum = round(ceil(count($_cb) / $_field['columns']));
        $_chk = array_chunk($_cb, $cnum);
        $cell = round(100 / $_field['columns']);
        foreach ($_chk as $chunk) {
            $_field['html'] .= '<div style="display:table-cell;width:' . $cell . '%">' . implode('<br>', $chunk) . '</div>';
        }
        $_field['html'] .= '</div></div>';
    }
    else {
        $_field['html'] = '<div style="display:inline-block;">' . implode('<br>', $_cb) . '</div>';
    }
    $_field['type']     = 'optionlist';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_optionlist_form_designer_options()
{
    return array(
        'options_help'        => 'you can enter available options ONE PER LINE, in the following format: <strong>Option Value|Option Text</strong> - you may also enter a valid module FUNCTION name that will return the options dynamically.',
        'disable_validation'  => true,
        'disable_min_and_max' => true
    );
}

/**
 * @ignore
 * jrCore_form_field_optionlist_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return bool
 */
function jrCore_form_field_optionlist_is_empty($_field, &$_post)
{
    $name  = $_field['name'];
    $_opts = array();
    foreach ($_post as $k => $v) {
        if (strpos($k, $name . '_') === 0 && ($v == 'on' || $v == 'off')) {
            $_opts[] = str_replace("{$name}_", '', $k);
        }
    }
    if (isset($_opts) && is_array($_opts) && count($_opts) > 0) {
        return false;
    }
    // Check for all being empty
    elseif (isset($_post[$name]) && $_post[$name] == 'off') {
        global $_post;
        $_post[$name] = 'off';
        return false;
    }
    return true;
}

/**
 * @ignore
 * jrCore_form_field_optionlist_assembly
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return array
 */
function jrCore_form_field_optionlist_assembly($_field, $_post)
{
    $name  = $_field['name'];
    $_opts = array();
    foreach ($_post as $ok => $ov) {
        if (strpos($ok, $name . '_') === 0) {
            if ($ov == 'on' || $ov == 'off') {
                $val     = str_replace("{$name}_", '', $ok);
                $_opts[] = $val;
            }
        }
    }
    if (count($_opts) > 0) {
        $_post[$name] = implode(',', $_opts);
    }
    else {
        $_post[$name] = '_';
    }
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_optionlist_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message for form validation
 * @return mixed
 */
function jrCore_form_field_optionlist_validate($_field, $_post, $e_msg)
{
    $name  = $_field['name'];
    $_keys = array();
    if (is_array($_field['options'])) {
        $_keys = $_field['options'];
    }
    elseif (isset($_field['options']) && strlen($_field['options']) > 0) {
        $func = $_field['options'];
        if (function_exists($func)) {
            $_keys = $func();
            if (!is_array($_keys)) {
                $_keys = array();
            }
        }
        else {
            $_keys = json_decode($_field['options'], true);
        }
    }
    // Make sure values are part of our options
    if (isset($_post[$name]) && strlen($_post[$name]) > 0) {
        foreach (explode(',', $_post[$name]) as $val) {
            if (!isset($_keys[$val]) && !in_array($val, $_keys)) {
                jrCore_set_form_notice('error', $e_msg);
                jrCore_form_field_hilight($name);
                return false;
            }
        }
    }
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_select_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 *
 * <!ELEMENT SELECT - - (OPTGROUP|OPTION)+ -- option selector -->
 * <!ATTLIST SELECT
 *   %attrs;                              -- %coreattrs, %i18n, %events --
 *   name        CDATA          #IMPLIED  -- field name --
 *   size        NUMBER         #IMPLIED  -- rows visible --
 *   multiple    (multiple)     #IMPLIED  -- default is single selection --
 *   disabled    (disabled)     #IMPLIED  -- unavailable in this context --
 *   tabindex    NUMBER         #IMPLIED  -- position in tabbing order --
 *   onfocus     %Script;       #IMPLIED  -- the element got the focus --
 *   onblur      %Script;       #IMPLIED  -- the element lost the focus --
 *   onchange    %Script;       #IMPLIED  -- the element value was changed --
 *   >
 */
function jrCore_form_field_select_display($_field, $_att = null)
{
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $cls = 'form_select' . jrCore_form_field_get_hilight($_field['name']);
    if (isset($_field['class']{0})) {
        $cls = "{$cls} {$_field['class']}";
    }
    $htm = '<select id="' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '" tabindex="' . $idx . '"';
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm .= '>';
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }
    if (isset($_field['options']) && is_array($_field['options'])) {
        foreach ($_field['options'] as $k => $v) {
            if (strlen($k) === 0) {
                continue;
            }
            // See if we have an OPT GROUP
            if (is_array($v)) {
                $htm .= '<optgroup label="' . $k . '">';
                foreach ($v as $kk => $vv) {
                    if (isset($_field['value']) && strlen($_field['value']) > 0 && $_field['value'] == "{$kk}") {
                        $htm .= '<option value="' . $kk . '" selected> ' . $vv . '</option>' . "\n";
                    }
                    elseif ((!isset($_field['value']) || strlen($_field['value']) === 0) && (isset($_field['default']) && $_field['default'] == "{$kk}")) {
                        $htm .= '<option value="' . $kk . '" selected> ' . $vv . '</option>' . "\n";
                    }
                    else {
                        $htm .= '<option value="' . $kk . '"> ' . $vv . '</option>' . "\n";
                    }
                }
                $htm .= '</optgroup>';
            }
            else {
                if (isset($_field['value']) && strlen($_field['value']) > 0 && $_field['value'] == "{$k}") {
                    $htm .= '<option value="' . $k . '" selected> ' . $v . '</option>' . "\n";
                }
                elseif ((!isset($_field['value']) || strlen($_field['value']) === 0) && (isset($_field['default']) && $_field['default'] == "{$k}")) {
                    $htm .= '<option value="' . $k . '" selected> ' . $v . '</option>' . "\n";
                }
                else {
                    $htm .= '<option value="' . $k . '"> ' . $v . '</option>' . "\n";
                }
            }
        }
    }
    $htm                .= '</select>';
    $_field['html']     = $htm;
    $_field['type']     = 'select';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_select_form_designer_options()
{
    return array(
        'options_help'        => 'you can enter select options ONE PER LINE, in the following format: <strong>Option Value|Option Text</strong> - you may also enter a valid module FUNCTION name that will return the options dynamically.',
        'disable_validation'  => true,
        'disable_min_and_max' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_select_attributes()
{
    return array('size', 'disabled', 'onfocus', 'onblur', 'onchange', 'class', 'style');
}

/**
 * @ignore
 * jrCore_form_field_select_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message for form validation
 * @return mixed
 */
function jrCore_form_field_select_validate($_field, $_post, $e_msg)
{
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }
    if (!isset($_field['options']) || !is_array($_field['options'])) {
        jrCore_set_form_notice('error', "invalid options received for field: {$_field['label']}");
        jrCore_form_result();
    }
    $name = $_post["{$_field['name']}"];
    // Our value must be in the option list
    if (!isset($_field['options'][$name])) {
        jrCore_set_form_notice('error', $e_msg);
        jrCore_form_field_hilight($_field['name']);
        return false;
    }
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_select_multiple_display
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 *
 * <!ELEMENT SELECT - - (OPTGROUP|OPTION)+ -- option selector -->
 * <!ATTLIST SELECT
 *   %attrs;                              -- %coreattrs, %i18n, %events --
 *   name        CDATA          #IMPLIED  -- field name --
 *   size        NUMBER         #IMPLIED  -- rows visible --
 *   multiple    (multiple)     #IMPLIED  -- default is single selection --
 *   disabled    (disabled)     #IMPLIED  -- unavailable in this context --
 *   tabindex    NUMBER         #IMPLIED  -- position in tabbing order --
 *   onfocus     %Script;       #IMPLIED  -- the element got the focus --
 *   onblur      %Script;       #IMPLIED  -- the element lost the focus --
 *   onchange    %Script;       #IMPLIED  -- the element value was changed --
 *   >
 */
function jrCore_form_field_select_multiple_display($_field, $_att = null)
{
    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $cls = 'form_select form_select_multiple' . jrCore_form_field_get_hilight($_field['name']);
    if (isset($_field['class']{0})) {
        $cls = "{$cls} {$_field['class']}";
    }
    $htm = '<select multiple="multiple" id="' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '[]" tabindex="' . $idx . '"';
    if (!isset($_att['size'])) {
        $_att['size'] = 8;
    }
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm .= '>';

    // Get selected values
    $_vl = array();
    if (!isset($_field['value']) && isset($_field['default'])) {
        $_field['value'] = $_field['default'];
    }
    if (isset($_field['value'])) {
        if (!is_array($_field['value']) && (strpos($_field['value'], '{') === 0 || strpos($_field['value'], '[') === 0)) {
            $_field['value'] = html_entity_decode($_field['value'], ENT_QUOTES, 'UTF-8');
            $_field['value'] = json_decode($_field['value'], true);
        }
        if (is_array($_field['value'])) {
            foreach ($_field['value'] as $v) {
                if (strlen($v) > 0) {
                    $_vl[$v] = 1;
                }
            }
        }
        elseif (!is_array($_field['value']) && strlen($_field['value']) > 0) {
            if (strpos($_field['value'], ',')) {
                foreach (explode(',', $_field['value']) as $v) {
                    $v = trim($v);
                    if (strlen($v) > 0) {
                        $_vl[$v] = 1;
                    }
                }
            }
            else {
                $_vl = array(trim($_field['value']) => 1);
            }
        }
    }

    // Get Options
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }
    if (isset($_field['options']) && is_array($_field['options'])) {
        foreach ($_field['options'] as $k => $v) {
            if (strlen($k) === 0) {
                continue;
            }
            if (isset($_vl[$k])) {
                $htm .= '<option value="' . $k . '" selected> ' . $v . '</option>' . "\n";
            }
            else {
                $htm .= '<option value="' . $k . '"> ' . $v . '</option>' . "\n";
            }
        }
    }
    $htm                .= '</select>';
    $_field['html']     = $htm;
    $_field['type']     = 'select_multiple';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * @ignore
 * jrCore_form_field_select_multiple_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return bool
 */
function jrCore_form_field_select_multiple_is_empty($_field, &$_post)
{
    $name = $_field['name'];
    if (!isset($_post[$name]) || !is_array($_post[$name])) {
        return true;
    }
    return false;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_select_multiple_form_designer_options()
{
    return array(
        'options_help'        => 'you can enter select options ONE PER LINE, in the following format: <strong>Option Value|Option Text</strong> - you may also enter a valid module FUNCTION name that will return the options dynamically.',
        'disable_validation'  => true,
        'disable_min_and_max' => true
    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_select_multiple_attributes()
{
    return array('size', 'disabled', 'onfocus', 'onblur', 'onchange', 'style', 'class', 'placeholder');
}

/**
 * @ignore
 * jrCore_form_field_select_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message for validation
 * @return mixed
 */
function jrCore_form_field_select_multiple_validate($_field, $_post, $e_msg)
{
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        // JSON encoded options
        if (strpos($_field['options'], '{') === 0 || strpos($_field['options'], '[') === 0) {
            $_field['options'] = json_decode($_field['options'], true);
        }
        // function
        elseif (function_exists($_field['options'])) {
            $_field['options'] = $_field['options']();
        }
    }
    if (!isset($_field['options']) || !is_array($_field['options'])) {
        jrCore_set_form_notice('error', "invalid options received for field: {$_field['label']}");
        jrCore_form_result();
    }
    // Our value will come in as an array
    if (!isset($_post["{$_field['name']}"]) || !is_array($_post["{$_field['name']}"])) {
        return false;
    }
    foreach ($_post["{$_field['name']}"] as $v) {
        // For each selected value submitted, it must be part of the options
        if (!isset($_field['options'][$v])) {
            jrCore_set_form_notice('error', $e_msg);
            return false;
        }
    }
    $_post["{$_field['name']}"] = implode(',', $_post["{$_field['name']}"]);
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_textarea
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 *
 * <!ELEMENT TEXTAREA - - (#PCDATA)       -- multi-line text field -->
 * <!ATTLIST TEXTAREA
 *   %attrs;                              -- %coreattrs, %i18n, %events --
 *   name        CDATA          #IMPLIED
 *   rows        NUMBER         #REQUIRED
 *   cols        NUMBER         #REQUIRED
 *   disabled    (disabled)     #IMPLIED  -- unavailable in this context --
 *   readonly    (readonly)     #IMPLIED
 *   tabindex    NUMBER         #IMPLIED  -- position in tabbing order --
 *   accesskey   %Character;    #IMPLIED  -- accessibility key character --
 *   onfocus     %Script;       #IMPLIED  -- the element got the focus --
 *   onblur      %Script;       #IMPLIED  -- the element lost the focus --
 *   onselect    %Script;       #IMPLIED  -- some text was selected --
 *   onchange    %Script;       #IMPLIED  -- the element value was changed --
 *   >
 */
function jrCore_form_field_textarea_display($_field, $_att = null)
{
    $cls = 'form_textarea' . jrCore_form_field_get_hilight($_field['name']);
    if (isset($_field['class']{0})) {
        $cls = "{$cls} {$_field['class']}";
    }
    if (isset($_field['options']) && !is_array($_field['options']) && strlen($_field['options']) > 0) {
        $_tmp = explode("\n", $_field['options']);
        if (isset($_tmp) && is_array($_tmp)) {
            foreach ($_tmp as $v) {
                $v = trim($v);
                if (strpos($v, '|')) {
                    list($k, $v) = explode('|', $v, 2);
                }
                else {
                    $k = $v;
                }
                $_field[$k] = $v;
            }
        }
    }

    $pch = '';
    if (isset($_field['placeholder'])) {
        $pch = ' placeholder="' . $_field['placeholder'] . '"';
    }

    // Get our tab index
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm = '<textarea' . $pch . ' id="' . $_field['name'] . '" class="' . $cls . '" name="' . $_field['name'] . '" tabindex="' . $idx . '"';
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $val = '';
    if (isset($_field['value']) && is_string($_field['value']) && strlen($_field['value']) > 0) {
        $val = $_field['value'];
    }
    elseif (isset($_field['default']) && strlen($_field['default']) > 0) {
        $val = $_field['default'];
    }
    $htm                .= '>' . $val . '</textarea>';
    $_field['html']     = $htm;
    $_field['type']     = 'textarea';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * Defines Form Designer field options
 * @return array
 */
function jrCore_form_field_textarea_form_designer_options()
{
    return array(
        'options_help' => 'If you want placeholder text for this textarea enter: <strong>placeholder|The text you want to show in the textarea</strong>',

    );
//
//    return array(
//        'disable_options' => true
//    );
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_textarea_attributes()
{
    return array('rows', 'cols', 'disabled', 'readonly', 'maxlength', 'onfocus', 'onblur', 'onselect', 'onchange', 'style', 'class', 'placeholder');
}

/**
 * @param array $_field Array of Field parameters
 * @param array $_att Additional HTML parameters
 * @return bool
 */
function jrCore_form_field_date_birthday_display($_field, $_att = null)
{
    // See if we have an existing value
    $dyr = 'YYYY';
    $dmn = null;
    $ddy = null;
    if (isset($_field['value']) && strlen($_field['value']) === 8) {
        // Our date must be in YYYYMMDD format
        $tmp = substr($_field['value'], 0, 4);
        if ($tmp > 0) {
            $dyr = $tmp;
        }
        $ddy = substr($_field['value'], 6, 2);
        $dmn = substr($_field['value'], 4, 2);
    }

    // Bring in lang strings
    $_ln = jrUser_load_lang_strings();

    // Month Selector
    $cls = 'form_date_birthday_month' . jrCore_form_field_get_hilight("{$_field['name']}_month");
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm = "\n" . '<select id="' . $_field['name'] . '_month" class="form_select ' . $cls . '" name="' . $_field['name'] . '_month" tabindex="' . $idx . '"';
    if (isset($_att['date_month']) && is_array($_att['date_month'])) {
        foreach ($_att as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm .= ">\n";

    $_op = array(
        '01' => $_ln['jrCore'][10],
        '02' => $_ln['jrCore'][11],
        '03' => $_ln['jrCore'][12],
        '04' => $_ln['jrCore'][13],
        '05' => $_ln['jrCore'][14],
        '06' => $_ln['jrCore'][15],
        '07' => $_ln['jrCore'][16],
        '08' => $_ln['jrCore'][17],
        '09' => $_ln['jrCore'][18],
        '10' => $_ln['jrCore'][19],
        '11' => $_ln['jrCore'][20],
        '12' => $_ln['jrCore'][21]
    );
    $htm .= '<option value=""></option>' . "\n";
    foreach ($_op as $k => $val) {
        if (!is_null($dmn) && $k == $dmn) {
            $htm .= '<option value="' . $k . '" selected> ' . $val . '</option>' . "\n";
        }
        else {
            $htm .= '<option value="' . $k . '"> ' . $val . '</option>' . "\n";
        }
    }
    $htm .= '</select>';

    // Day
    $cls = 'form_date_birthday_day' . jrCore_form_field_get_hilight("{$_field['name']}_day");
    $idx = jrCore_form_field_get_tab_index($_field);
    $htm .= "\n" . '<select id="' . $_field['name'] . '_day" class="form_select ' . $cls . '" name="' . $_field['name'] . '_day" tabindex="' . $idx . '"';
    if (isset($_att['day']) && is_array($_att['day'])) {
        foreach ($_att['day'] as $key => $attr) {
            $htm .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $htm .= ">\n";

    $htm .= '<option value="" selected></option>' . "\n";
    $num = 1;
    while ($num < 32) {
        $knum = (string) $num;
        if (strlen($knum) == 1) {
            $knum = '0' . $knum;
        }
        if (!is_null($ddy) && $num == $ddy) {
            $htm .= '<option value="' . $knum . '" selected> ' . $num . '</option>' . "\n";
        }
        else {
            $htm .= '<option value="' . $knum . '"> ' . $num . '</option>' . "\n";
        }
        $num++;
    }
    $htm .= '</select>';

    // Year
    if (!isset($_field['exclude_year']) || $_field['exclude_year'] === false) {
        $cls = 'form_date_birthday_year' . jrCore_form_field_get_hilight("{$_field['name']}_year");
        $idx = jrCore_form_field_get_tab_index($_field);
        $htm .= '<input type="text" id="' . $_field['name'] . '_year" class="form_text ' . $cls . '" name="' . $_field['name'] . '_year" value="' . $dyr . '" tabindex="' . $idx . '" onfocus="if(this.value==\'YYYY\'){this.value=\'\';}" onblur="if(this.value==\'\'){this.value=\'YYYY\';}">';
    }

    $_field['html']     = $htm;
    $_field['type']     = 'date_birthday';
    $_field['template'] = 'form_field_elements.tpl';
    jrCore_create_page_element('page', $_field);
    return true;
}

/**
 * @ignore
 * Additional form field HTML attributes that can be passed in via the form
 * @return array
 */
function jrCore_form_field_date_birthday_attributes()
{
    return array('disabled', 'onfocus', 'onblur', 'onchange', 'style', 'class');
}

/**
 * @ignore
 * jrCore_form_field_select_and_text_is_empty
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return bool
 */
function jrCore_form_field_date_birthday_is_empty($_field, $_post)
{
    return false;
}

/**
 * Checks to see if we received data on our post in the form validator
 * @param array $_field Array of Field Parameters
 * @param array $_post Posted Data for checking
 * @return array
 */
function jrCore_form_field_date_birthday_assembly($_field, $_post)
{
    // We need to take our multiple form field entries
    // and assemble them into yyyymmdd format:
    $name = $_field['name'];
    if (isset($_post["{$name}_year"])) {
        if ($_post["{$name}_year"] == 'YYYY') {
            $_post["{$name}_year"] = '0000';
        }
        elseif (strlen($_post["{$name}_year"]) !== 4) {
            $_post["{$name}_year"] = '0000';
        }
    }
    else {
        $_post["{$name}_year"] = '0000';
    }
    if (!isset($_post["{$name}_month"]) || strlen($_post["{$name}_month"]) !== 2) {
        $_post["{$name}_month"] = '00';
    }
    if (!isset($_post["{$name}_day"]) || strlen($_post["{$name}_day"]) !== 2) {
        $_post["{$name}_day"] = '00';
    }
    $_post[$name] = $_post["{$name}_year"] . $_post["{$name}_month"] . $_post["{$name}_day"];
    return $_post;
}

/**
 * @ignore
 * jrCore_form_field_select_and_text_validate
 * @param array $_field Array of form field info
 * @param array $_post Global $_post from jrCore_parse_url()
 * @param string $e_msg Error message to use in validation
 * @return mixed
 */
function jrCore_form_field_date_birthday_validate($_field, $_post, $e_msg)
{
    $name = $_field['name'];

    // OK if all three are blank and not required
    if ((!isset($_field['required']) || $_field['required'] != 1) && $_post["{$name}_year"] == '' && $_post["{$name}_month"] == '' && $_post["{$name}_day"] == '') {
        return $_post;
    }

    // Year
    if (isset($_post["{$name}_year"]) && (strlen($_post["{$name}_year"]) != 4 || $_post["{$name}_year"] == 'YYYY' || !jrCore_checktype($_post["{$name}_year"], 'number_nz'))) {
        if (isset($_field['required']) && $_field['required'] == '1') {
            $_ln = jrUser_load_lang_strings();
            jrCore_set_form_notice('error', $_ln['jrCore'][93]);
            jrCore_form_field_hilight("{$name}_year");
            return false;
        }
    }
    unset($_post["{$name}_year"]);

    // Month
    if (isset($_post["{$name}_month"]) && (strlen($_post["{$name}_month"]) != 2 || !jrCore_checktype($_post["{$name}_month"], 'number_nz'))) {

        // We only complain if required...
        if (isset($_field['required']) && $_field['required'] == '1') {
            $_ln = jrUser_load_lang_strings();
            jrCore_set_form_notice('error', $_ln['jrCore'][94]);
            jrCore_form_field_hilight("{$name}_month");
            return false;
        }
    }
    unset($_post["{$name}_month"]);

    // Day
    if (isset($_post["{$name}_day"]) && (strlen($_post["{$name}_day"]) != 2 || !jrCore_checktype($_post["{$name}_day"], 'number_nz'))) {
        if (isset($_field['required']) && $_field['required'] == '1') {
            $_ln = jrUser_load_lang_strings();
            jrCore_set_form_notice('error', $_ln['jrCore'][95]);
            jrCore_form_field_hilight("{$name}_day");
            return false;
        }
    }
    unset($_post["{$name}_day"]);

    return $_post;
}

/**
 * @ignore
 * jrCore_form_modal_window
 * @param string $module Module Name
 * @param array $_form Form Information from existing Form Session
 * @return bool
 */
function jrCore_form_modal_window($module, $_form)
{
    global $_lang;
    $note = '';
    if (isset($_form) && isset($_form['modal_note'])) {
        $note = $_form['modal_note'];
        if (jrCore_checktype($_form['modal_note'], 'number_nz')) {
            $note = $_lang[$module]["{$_form['modal_note']}"];
        }
        $note = '<div class="page_notice notice">' . $note . '</div>';
    }
    $_tmp = array(
        'type'          => 'form_modal_window',
        'note'          => $note,
        'html'          => '<div id="modal_updates"></div>',
        'module'        => 'jrCore',
        'template'      => 'form_modal.tpl',
        'modal_width'   => (isset($_form['modal_width']) && jrCore_checktype($_form['modal_width'], 'number_nz')) ? (int) $_form['modal_width'] : 500,
        'modal_height'  => (isset($_form['modal_height']) && jrCore_checktype($_form['modal_height'], 'number_nz')) ? (int) $_form['modal_height'] : 500,
        'modal_close'   => (isset($_form['modal_close']) && strlen($_form['modal_close']) > 0) ? trim($_form['modal_close']) : '',
        'modal_onclick' => (isset($_form['modal_onclick']) && strlen($_form['modal_onclick']) > 0) ? trim($_form['modal_onclick']) : ''
    );
    jrCore_create_page_element('form_modal', $_tmp);
    return true;
}

/**
 * @ignore
 * jrCore_form_submit
 * @param string $submit_value Value for Submit Button
 * @param string $clear_value Value for Reset Button (set to false to disable)
 * @param string|bool $cancel_value Value for Cancel Button (set to false to disable)
 * @param string|bool $cancel_url URL to load when Cancel button is pressed
 * @return bool
 */
function jrCore_form_submit($submit_value = 'submit', $clear_value = 'reset', $cancel_value = false, $cancel_url = false)
{
    // Get our form id
    $form_id = jrCore_get_flag('jr_form_create_active_form_id');

    // Have we already shown this submit in this form?
    if (jrCore_get_flag("jr_form_submit_shown_{$form_id}")) {
        return true;
    }
    jrCore_set_flag("jr_form_submit_shown_{$form_id}", 1);

    // Get our tab index
    $tab = jrCore_get_flag('jr_form_tab_index');
    if (!$tab) {
        $tab = 1;
    }

    // See if we are including a spam checkbox
    $sbc = jrCore_get_flag('jrcore_form_field_checkbox_spambot');
    if ($sbc) {
        // We are including a spam checkbox - set it up on page load
        $_tmp = array("jrFormSpamBotCheckbox('{$sbc}',{$tab});");
        jrCore_create_page_element('javascript_ready_function', $_tmp);
        jrCore_set_flag('jr_form_tab_index', ++$tab);
    }

    if (isset($form_id{0})) {
        $key = "'{$form_id}'";
    }
    else {
        $key = 'false';
    }
    $_form = jrCore_form_get_session($form_id);
    $_lang = jrUser_load_lang_strings();

    // Make sure we have a good submit value...
    if (strlen($submit_value) === 0 && $submit_value !== false) {
        $submit_value = $_lang['jrCore'][1];
    }

    // $_form['form_params']:
    // [submit_value] => save changes
    // [action] => admin_save/global
    // [module] => jrCore
    // [name] => jrCore_admin
    // [token] => 7cca3cf029f38ddf1ec1472a5825f817

    $_js = array();
    if (isset($_form['form_params']['onclick'])) {
        $_js[] = $_form['form_params']['onclick'];
    }
    $module = $_form['form_params']['module'];

    // By default we submit via AJAX, but if we have disabled AJAX upload
    // or have the progress meter on page, we do a normal submit
    $submit = 'ajax';
    if (isset($_form['form_params']['submit_modal']{0})) {
        jrCore_form_modal_window($module, $_form['form_params']);
        $submit = 'modal';
    }
    elseif (isset($_form['form_params']['form_ajax_submit']) && $_form['form_params']['form_ajax_submit'] === false) {
        $submit = 'post';
    }

    // See if we are doing a submit prompt
    if (isset($_form['form_params']['submit_prompt']{0})) {
        $txt = $_form['form_params']['submit_prompt'];
        if (isset($_form['form_params']['submit_prompt']) && isset($_lang[$module]["{$_form['form_params']['submit_prompt']}"])) {
            $txt = $_lang[$module]["{$_form['form_params']['submit_prompt']}"];
        }
        $ttl = 'Are you sure?';
        if (isset($_form['form_params']['submit_title'])) {
            if (isset($_lang[$module]["{$_form['form_params']['submit_title']}"])) {
                $ttl = $_lang[$module]["{$_form['form_params']['submit_title']}"];
            }
            else {
                $ttl = $_form['form_params']['submit_title'];
            }
        }
        $_form['form_params']['onclick'] = "jrCore_confirm('" . addslashes($ttl) . "', '" . addslashes($txt) . "', function(){ jrFormSubmit('#{$_form['form_params']['name']}',{$key},'{$submit}') })";
    }
    elseif (isset($_js) && count($_js) > 0) {
        $_form['form_params']['onclick'] = 'var jrc=true;' . implode(' ', $_js) . " if (jrc){jrFormSubmit('#{$_form['form_params']['name']}',{$key},'{$submit}');} else {return false;}";
    }
    else {
        $_form['form_params']['onclick'] = "jrFormSubmit('#{$_form['form_params']['name']}',{$key},'{$submit}');";
    }

    // Create Submit Button
    $html = '';
    if ($submit_value) {
        $html = '<input type="button" id="' . $_form['form_params']['name'] . '_submit" class="form_button" value="' . $submit_value . '" tabindex="' . $tab . '" onclick="' . $_form['form_params']['onclick'] . '">';
    }

    // Create Undo Button
    if ($clear_value) {
        if ($clear_value == 'reset') {
            $clear_value = $_lang['jrCore'][9];
        }
        if (strlen($html) > 0) {
            $html .= '&nbsp;&nbsp;';
        }
        $html .= '<input type="reset" id="' . $_form['form_params']['name'] . '_reset" class="form_button" value="' . $clear_value . '">';
    }
    // and cancel button
    if ($cancel_value || $cancel_url) {
        if (!$cancel_value) {
            $cancel_value = $_lang['jrCore'][2];
        }

        // Handle referring URLs
        if ($cancel_url == 'referrer') {
            $cancel_url = jrCore_get_local_referrer();
        }
        elseif ($cancel_url == 'reset_form_referrer') {
            if (isset($_SESSION['reset_form_referrer_url'])) {
                $cancel_url = $_SESSION['reset_form_referrer_url'];
            }
            else {
                $cancel_url = jrCore_get_local_referrer();
                if (!strpos($cancel_url, '_rfs=')) {
                    if (strpos($cancel_url, '?')) {
                        $cancel_url .= "&_rfs={$form_id}";
                    }
                    else {
                        $cancel_url .= "?_rfs={$form_id}";
                    }
                }
                $_SESSION['reset_form_referrer_url'] = $cancel_url;
            }
        }

        if ($cancel_url == '$.modal.close();') {
            $html .= '&nbsp;&nbsp;<input type="button" id="' . $_form['form_params']['name'] . '_cancel" class="form_button" value="' . $cancel_value . '" onclick="$.modal.close();">';
        }
        elseif (jrCore_checktype($cancel_url, 'url')) {
            $html .= '&nbsp;&nbsp;<input type="button" id="' . $_form['form_params']['name'] . '_cancel" class="form_button" value="' . $cancel_value . '" onclick="jrCore_window_location(\'' . $cancel_url . '\')">';
        }
        elseif (!empty($cancel_url)) {
            $html .= '&nbsp;&nbsp;<input type="button" id="' . $_form['form_params']['name'] . '_cancel" class="form_button" value="' . $cancel_value . '" onclick="' . $cancel_url . '">';
        }
        else {
            $cancel_url = jrCore_get_last_history_url();
            $html       .= '&nbsp;&nbsp;<input type="button" id="' . $_form['form_params']['name'] . '_cancel" class="form_button" value="' . $cancel_value . '" onclick="jrCore_window_location(\'' . $cancel_url . '\')">';
        }
    }
    $_tmp = array(
        'type'     => 'form_submit',
        'form_id'  => $form_id,
        'html'     => $html,
        'module'   => 'jrCore',
        'template' => 'form_submit.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    jrCore_set_flag('jr_form_tab_index', ++$tab);
    return true;
}

/**
 * Set an error/notice message in a Modal Popup window
 * @param string $type Type of Notice (error,notice,success,warning)
 * @param string $text Text for Notice
 * @return bool
 */
function jrCore_form_modal_notice($type, $text)
{
    global $_post;
    if (isset($_post['jr_html_modal_token'])) {
        $key = jrCore_db_escape($_post['jr_html_modal_token']);
        $tmp = array(
            't' => $type,
            'm' => substr(jrCore_strip_emoji($text, false), 0, 128)
        );
        $tbl = jrCore_db_table_name('jrCore', 'modal');
        $req = "INSERT INTO {$tbl} (modal_key,modal_updated,modal_value) VALUES ('{$key}',UNIX_TIMESTAMP(),'" . jrCore_db_escape(json_encode($tmp)) . "')";
        $iid = jrCore_db_query($req, 'INSERT_ID');
        if ($iid && $iid > 0) {
            return true;
        }
    }
    return false;
}

/**
 * Cleanup temp entries at the end of a Modal Window session
 *
 * @param string $key Form Token
 * @return bool
 */
function jrCore_form_modal_cleanup($key)
{
    $key = jrCore_db_escape($key);
    $tbl = jrCore_db_table_name('jrCore', 'modal');
    $req = "DELETE FROM {$tbl} WHERE modal_key = '{$key}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt > 0) {
        return true;
    }
    return false;
}

/**
 * @ignore
 * jrCore_form_end - end an open form
 * @return bool
 */
function jrCore_form_end()
{
    $_tmp = array(
        'type'      => 'form_end',
        'form_html' => "</form>\n",
        'module'    => 'jrCore',
        'template'  => 'form_end.tpl'
    );
    jrCore_create_page_element('form_end', $_tmp);
    return true;
}

/**
 * Generate a unique MD5 based token for CSRF validation
 * @param string $extra Extra info to add to md5 for token creation
 * @return string Returns created Token
 */
function jrCore_form_token_create($extra = '')
{
    global $_post, $_conf;
    $uid = (isset($_SESSION['_user_id'])) ? $_SESSION['_user_id'] : '';
    $pid = (isset($_SESSION['user_active_profile_id'])) ? $_SESSION['user_active_profile_id'] : '';
    $uri = (!empty($_post['_uri'])) ? $_post['_uri'] : '/';
    return md5($_conf['jrCore_base_url'] . $uri . session_id() . $uid . $pid . json_encode($extra));
}

/**
 * Validate a window.location redirect URL has been set for CSRF purposes
 * @return bool
 */
function jrCore_validate_location_url()
{
    global $_conf;
    if (isset($_COOKIE['jr_location_url']{1})) {
        // Make sure we've come from the correct URL
        if (!strpos($_COOKIE['jr_location_url'], $_SERVER['REQUEST_URI'])) {
            // Check QUERY_STRING - normally this is not needed, but on some
            // redirects the params can get double encoded - QUERY_STRING will have it right
            // [QUERY_STRING] => _uri=networklicense/host_remove/aHR0cDovL3d3dy5wcm94aW1hY29yZS5jb20%253D
            if (isset($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], '_uri=') === 0) {
                list(, $uri) = explode('=', $_SERVER['QUERY_STRING']);
                if (strpos($_COOKIE['jr_location_url'], $uri)) {
                    jrCore_delete_cookie('jr_location_url');
                    return true;
                }
            }
        }
        // We're good...
        return true;
    }
    // Without a cookie, it could be a cross domain issue - check
    if (jrUser_is_logged_in()) {
        $ref = str_replace('https:', 'http:', jrCore_get_local_referrer());
        $url = str_replace('https:', 'http:', jrUser_get_saved_url_location());
        if ($ref && strpos(' ' . $ref, $url) || strpos($url, $_conf['jrCore_base_url']) === 0) {
            return true;
        }
    }
    else {
        // Users that are not logged in cannot be checked
        return true;
    }
    jrCore_notice_page('error', 'invalid location redirect token received - please try again');
    return false;
}

/**
 * jrCore_form_check_default_attributes will check that a default set of
 * attributes for an HTML element has been provided.
 * @param array $_default Default values for attributes
 * @param array $_attrs Attributes passed in to function
 * @return array returns Attributes with any defaults added in
 */
function jrCore_form_check_default_attributes($_default, $_attrs = null)
{
    // Make sure our defaults are setup
    if (is_null($_attrs) || !isset($_attrs) || !is_array($_attrs)) {
        $_attrs = array();
    }
    foreach ($_default as $key => $val) {
        // If we receive a default value that is an array, it means the
        // allowed values must be one of the array elements.
        if (is_array($val)) {
            if (!isset($_attrs[$key]) || (isset($_attrs[$key]) && !in_array($_attrs[$key], $val))) {
                // If we are not set, or not allowed, we use the FIRST
                // element in our default value array
                $_attrs[$key] = reset($val);
            }
        }
        elseif (!isset($_attrs[$key]) || strlen($_attrs[$key]) === 0) {
            $_attrs[$key] = $val;
        }
    }
    return $_attrs;
}

//---------------------------
// FORM SESSIONS
//---------------------------

/**
 * Get the active form session plugin
 * @note: form_sessions follow the active user session plugin
 * @return string
 */
function jrCore_get_active_form_session_system()
{
    global $_conf;
    if (isset($_conf['jrUser_active_session_system']{1})) {
        // Make sure it is valid...
        $func = "_{$_conf['jrUser_active_session_system']}_form_create_session";
        if (function_exists($func)) {
            return $_conf['jrUser_active_session_system'];
        }
    }
    return 'jrCore_mysql';
}

/**
 * Create a new Form Session
 * @param string $form_id Form ID from jrCore_form_token_create()
 * @param array $_form Form Information
 * @return bool
 */
function jrCore_form_create_session($form_id, $_form)
{
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_create_session";
    if (function_exists($func)) {
        $tmp = $func($form_id, $_form);
        // Set an internal flag so any form functions can get the currently generated form token/form_id
        jrCore_set_flag("jrcore_form_get_session_{$form_id}", $tmp);
        jrCore_set_flag('jr_form_create_active_form_id', $form_id);
        return true;
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Get information about the active form session
 * @param string $form_id Form ID to get session for
 * @return mixed
 */
function jrCore_form_get_session($form_id = null)
{
    if (is_null($form_id) || $form_id === false) {
        if (!$form_id = jrCore_get_flag('jr_form_create_active_form_id')) {
            // bad session
            return false;
        }
    }

    // Check for cache
    if ($_rt = jrCore_get_flag("jrcore_form_get_session_{$form_id}")) {
        return $_rt;
    }
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_get_session";
    if (function_exists($func)) {
        if ($_rt = $func($form_id)) {
            jrCore_set_flag("jrcore_form_get_session_{$form_id}", $_rt);
            return $_rt;
        }
        // No form session from plugin
        return false;
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Save a form session
 * @param string $form_id Form ID of existing form session
 * @param array $_data Form information to save
 * @return bool
 */
function jrCore_form_save_session($form_id, $_data)
{
    $_rt = jrCore_form_get_session($form_id);
    if (!isset($_rt) || !is_array($_rt) || !isset($_rt['form_token'])) {
        // Form Session must have been previously created before it can be saved
        return false;
    }
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_save_session";
    if (function_exists($func)) {
        return $func($form_id, $_data, $_rt);
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Update a form session with new form fields (form designer)
 * @param string $form_id Form ID of existing form session
 * @param array $_fields New form fields data
 * @return bool
 */
function jrCore_form_update_session_fields($form_id, $_fields)
{
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_update_session_fields";
    if (function_exists($func)) {
        return $func($form_id, $_fields);
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Update a form session with new form fields (form designer)
 * @param string $form_id Form ID of existing form session
 * @return bool
 */
function jrCore_form_mark_as_validated($form_id)
{
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_mark_as_validated";
    if (function_exists($func)) {
        return $func($form_id);
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Delete an Active form session
 * @param string $form_id Form ID to delete
 * @return bool
 */
function jrCore_form_delete_session($form_id = null)
{
    global $_post;
    if (is_null($form_id) || $form_id === false) {
        if (isset($_post['jr_html_form_token']{0})) {
            $form_id                                   = trim($_post['jr_html_form_token']);
            $_SESSION['jrcore_form_notice_last_token'] = $form_id;
        }
        else {
            return false;
        }
    }
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_delete_session";
    if (function_exists($func)) {
        $tmp = $func($form_id);
        // Clean up any file uploads
        if (isset($_post['upload_token']{0})) {
            $dir = jrCore_get_upload_temp_directory($_post['upload_token']);
            if (is_dir($dir)) {
                jrCore_delete_dir_contents($dir);
                rmdir($dir);
            }
        }
        session_regenerate_id();
        return $tmp;
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Delete all form sessions for a module/view
 * @param string $module Module that contains the View
 * @param string $view View to delete
 * @return bool
 */
function jrCore_form_delete_session_view($module, $view)
{
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_delete_session_view";
    if (function_exists($func)) {
        return $func($module, $view);
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Perform form session maintenance (cleanup, etc.)
 * @return bool
 */
function jrCore_form_session_maintenance()
{
    global $_conf;
    $hours = 4;
    if (isset($_conf['jrCore_form_session_expire_hours']) && jrCore_checktype($_conf['jrCore_form_session_expire_hours'], 'number_nz')) {
        $hours = (int) $_conf['jrCore_form_session_expire_hours'];
    }
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_session_maintenance";
    if (function_exists($func)) {
        return $func(intval($hours));
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Delete all form session
 * @return bool
 */
function jrCore_form_delete_all_form_sessions()
{
    $temp = jrCore_get_active_form_session_system();
    $func = "_{$temp}_form_delete_all_form_sessions";
    if (function_exists($func)) {
        return $func();
    }
    jrCore_logger('CRI', "active form session system function: {$func} is not defined");
    return false;
}

/**
 * Add a form field to a form session
 * @param string $form_id Form ID of the form session
 * @param array $_field Field information to add to form
 * @return bool
 */
function jrCore_form_add_field_to_session($form_id, $_field)
{
    $_tmp = jrCore_get_flag('jrcore_form_session_fields');
    if (!$_tmp) {
        $_tmp = array();
    }
    $_tmp[] = $_field;
    jrCore_set_flag('jrcore_form_session_fields', $_tmp);
    return true;
}

/**
 * Delete a form field from an existing form session
 * @param string $form_id form ID of existing form session
 * @param string $field_name field 'name' to delete
 * @return bool
 */
function jrCore_form_delete_field_from_session($form_id, $field_name)
{
    if ($_tmp = jrCore_get_flag('jrcore_form_session_fields')) {
        foreach ($_tmp as $k => $_field) {
            if ($_field['name'] == $field_name) {
                unset($_tmp[$k]);
                jrCore_set_flag('jrcore_form_session_fields', $_tmp);
                return true;
            }
        }
    }
    return false;
}

//---------------------------
// FORM SESSION PLUGINS
//---------------------------

/**
 * Create a new Form Session
 * @param string $form_id Form ID from jrCore_form_token_create()
 * @param array $_form Form Information
 * @return bool
 */
function _jrCore_mysql_form_create_session($form_id, $_form)
{
    global $_post, $_user;
    // Make sure form session is created for this id
    $uid = (isset($_SESSION['_user_id']) && jrCore_checktype($_SESSION['_user_id'], 'number_nz')) ? intval($_SESSION['_user_id']) : '0';
    $opt = jrCore_db_escape("{$_post['module']}/{$_post['option']}");
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    // Do we already have an existing session?
    $_rt = jrCore_form_get_session($form_id);
    if (!$_rt || !is_array($_rt) || !isset($_rt['form_token'])) {
        $req = "INSERT INTO {$tbl} (form_token,form_created,form_user_id,form_view,form_params,form_fields,form_saved)
                VALUES ('{$form_id}',UNIX_TIMESTAMP(),'{$uid}','{$opt}','" . jrCore_db_escape(json_encode($_form)) . "','','')
                ON DUPLICATE KEY UPDATE form_created = UNIX_TIMESTAMP(), form_view = '{$opt}', form_params = '" . jrCore_db_escape(json_encode($_form)) . "'";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt === 0) {
            jrCore_notice('Error', 'unable to store form session - check Activity Log');
        }
        // Set flag so any get_sessions in this process won't have to come back to the DB
        $_rt = array(
            'form_token'      => $form_id,
            'form_created'    => time(),
            'form_updated'    => 0,
            'form_rand'       => 0,
            'form_user_id'    => $uid,
            'form_profile_id' => ($uid > 0) ? $_user['user_active_profile_id'] : 0,
            'form_view'       => "{$_post['module']}/{$_post['option']}",
            'form_validated'  => 0,
            'form_params'     => $_form,
            'form_fields'     => '',
            'form_saved'      => ''
        );
    }
    else {
        // Update with new session info
        $req = "UPDATE {$tbl} SET form_created = UNIX_TIMESTAMP(), form_rand = '" . mt_rand(0, 999999999) . "', form_view = '{$opt}', form_params = '" . jrCore_db_escape(json_encode($_form)) . "'
                 WHERE form_token = '" . jrCore_db_escape($form_id) . "' LIMIT 1";
        $cnt = jrCore_db_query($req, 'COUNT');
        if (!$cnt || $cnt === 0) {
            jrCore_notice('Error', 'unable to update form session - check Activity Log');
        }
    }
    return $_rt;
}

/**
 * Get information about the active form session
 * @param string $form_id Form ID to get session for
 * @return mixed
 */
function _jrCore_mysql_form_get_session($form_id = null)
{
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $tkn = jrCore_db_escape($form_id);
    $uid = (isset($_SESSION['_user_id'])) ? intval($_SESSION['_user_id']) : '0';
    $req = "SELECT * FROM {$tbl} WHERE form_token = '{$tkn}' AND form_user_id = '{$uid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if (!$_rt || !is_array($_rt) || !isset($_rt['form_token'])) {
        return false;
    }
    // form_params - parameters for the form
    $_rt['form_params'] = (isset($_rt['form_params']{2})) ? json_decode($_rt['form_params'], true) : false;

    // form_fields - information about each field in the form
    $_rt['form_fields'] = (isset($_rt['form_fields']{2})) ? json_decode($_rt['form_fields'], true) : false;

    // form_saved - if the user enters info an encounters an error, the values they entered are saved here
    $_rt['form_saved'] = (isset($_rt['form_saved']{2})) ? json_decode($_rt['form_saved'], true) : false;

    return $_rt;
}

/**
 * Save a form session
 * @param string $form_id Form ID of existing form session
 * @param array $_data Form information to save
 * @param array $_form existing form information
 * @return bool
 */
function _jrCore_mysql_form_save_session($form_id, $_data, $_form)
{
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $tkn = jrCore_db_escape($form_id);
    $sav = jrCore_db_escape(json_encode($_data));
    $req = "UPDATE {$tbl} SET form_updated = UNIX_TIMESTAMP(), form_rand = '" . mt_rand() . "', form_saved = '{$sav}' WHERE form_token = '{$tkn}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Save a form session
 * @param string $form_id Form ID of existing form session
 * @param array $_fields form field data
 * @return bool
 */
function _jrCore_mysql_form_update_session_fields($form_id, $_fields)
{
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $tkn = jrCore_db_escape($form_id);
    $sav = jrCore_db_escape(json_encode($_fields));
    $req = "UPDATE {$tbl} SET form_updated = UNIX_TIMESTAMP(), form_rand = '" . mt_rand() . "', form_fields = '{$sav}' WHERE form_token = '{$tkn}'";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        return true;
    }
    return false;
}

/**
 * Mark a form session as validated
 * @param string $form_id Form ID to delete
 * @return bool
 */
function _jrCore_mysql_form_mark_as_validated($form_id)
{
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $req = "UPDATE {$tbl} SET form_rand = '" . mt_rand() . "', form_validated = '1' WHERE form_token = '{$form_id}' LIMIT 1";
    return jrCore_db_query($req);
}

/**
 * Delete an Active form session
 * @param string $form_id Form ID to delete
 * @return bool
 */
function _jrCore_mysql_form_delete_session($form_id)
{
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $tkn = jrCore_db_escape($form_id);
    $req = "DELETE FROM {$tbl} WHERE form_token = '{$tkn}'";
    jrCore_db_query($req);
    return true;
}

/**
 * @ignore
 * jrCore_form_delete_session_view
 * @param string $module Module that contains the View
 * @param string $view View to delete
 * @return bool
 */
function _jrCore_mysql_form_delete_session_view($module, $view)
{
    $mod = jrCore_db_escape($module);
    $opt = jrCore_db_escape($view);
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $req = "DELETE FROM {$tbl} WHERE form_view = '{$mod}/{$opt}'";
    jrCore_db_query($req);
    return true;
}

/**
 * Perform form session maintenance
 * @param int $hours
 * @return bool
 */
function _jrCore_mysql_form_session_maintenance($hours)
{
    // Cleanup old form sessions
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $req = "SELECT form_token FROM {$tbl} WHERE form_updated < (UNIX_TIMESTAMP() - ({$hours} * 3600))";
    $_rt = jrCore_db_query($req, 'form_token', false, 'form_token');
    if ($_rt && is_array($_rt)) {
        $req = "DELETE FROM {$tbl} WHERE form_token IN('" . implode("','", $_rt) . "')";
        jrCore_db_query($req);
    }
    return true;
}

/**
 * Delete all existing form sessions
 * @return mixed
 */
function _jrCore_mysql_form_delete_all_form_sessions()
{
    $tbl = jrCore_db_table_name('jrCore', 'form_session');
    $req = "TRUNCATE TABLE {$tbl}";
    return jrCore_db_query($req);
}
