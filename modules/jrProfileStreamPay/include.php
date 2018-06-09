<?php
/**
 * Jamroom Stream Pay module
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
 * @copyright 2015 Talldude Networks, LLC.
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * meta
 */
function jrProfileStreamPay_meta()
{
    $_tmp = array(
        'name'        => 'Stream Pay',
        'url'         => 'profilestreampay',
        'version'     => '1.0.9',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Pay Profiles for audio and video items streamed by users',
        'license'     => 'jcl',
        'category'    => 'profiles',
        'priority'    => 255,
        'requires'    => 'jrCore:6.0.4'
    );
    return $_tmp;
}

/**
 * init
 */
function jrProfileStreamPay_init()
{
    $_tmp = array(
        'label' => 'Enable Stream Credit',
        'help'  => 'If checked, audio and video streams in this Quota will be credited for plays IF configured with a positive stream amount below.'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrProfileStreamPay', 'on', $_tmp);
    jrCore_register_event_listener('jrCore', 'stream_file', 'jrProfileStreamPay_stream_file_listener');

    // Add custom field for payout amount
    jrCore_register_event_listener('jrCore', 'form_display', 'jrProfileStreamPay_form_display_listener');

    // System reset listener
    jrCore_register_event_listener('jrDeveloper', 'reset_system', 'jrProfileStreamPay_reset_system_listener');

    // Our credit browser
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrProfileStreamPay', 'credit_log', array('Credit Log', 'Browse the Stream Pay Credit Log'));

    // Custom tab
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrProfileStreamPay', 'credit_log', 'Credit Log');

    jrCore_register_module_feature('jrCore', 'javascript', 'jrProfileStreamPay', true);

    return true;
}

//----------------------------
// EVENT LISTENERS
//----------------------------

/**
 * System Reset listener
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProfileStreamPay_reset_system_listener($_data, $_user, $_conf, $_args, $event)
{
    $tbl = jrCore_db_table_name('jrProfileStreamPay', 'log');
    jrCore_db_query("TRUNCATE TABLE {$tbl}");
    jrCore_db_query("OPTIMIZE TABLE {$tbl}");
    return $_data;
}

/**
 * Credit a profile for an audio/video stream
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrProfileStreamPay_stream_file_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (!jrUser_is_admin() && isset($_data['quota_jrProfileStreamPay_allowed']) && $_data['quota_jrProfileStreamPay_allowed'] == 'on') {
        // We are enabled for this profile - check if we are adding a balance
        switch ($_args['module']) {
            case 'jrAudio':
            case 'jrVideo':
                $amt = 0;
                $pfx = jrCore_db_get_prefix($_post['module']);
                if (isset($_data["{$pfx}_stream_amount"]) && $_data["{$pfx}_stream_amount"] > 0) {
                    // We have a custom amount on the individual item
                    $amt = $_data["{$pfx}_stream_amount"];
                }
                elseif (isset($_data["quota_jrProfileStreamPay_{$pfx}_amount"]) && $_data["quota_jrProfileStreamPay_{$pfx}_amount"] > 0) {
                    // We are paying a set amount based on quota
                    $amt = $_data["quota_jrProfileStreamPay_{$pfx}_amount"];
                }
                if ($amt > 0) {
                    $amt = ($amt / 100);
                    $old = 10000;
                    if (isset($_data['quota_jrProfileStreamPay_reset']) && jrCore_checktype($_data['quota_jrProfileStreamPay_reset'], 'number_nz')) {
                        $old = (int) $_data['quota_jrProfileStreamPay_reset'];
                    }
                    $old = ($old * 86400);
                    if (jrCore_counter_is_unique_viewer('jrProfileStreamPay', $_args['item_id'], "{$_args['module']}_{$_args['item_id']}_stream", $old)) {
                        // We have a unique stream - credit
                        if (jrCore_db_increment_key('jrProfile', $_data['_profile_id'], 'profile_balance', $amt)) {
                            jrProfileStreamPay_log_credit_entry($_args['module'], $_args['item_id'], $_data["{$pfx}_title"], $amt);
                        }
                    }
                }
                break;
        }
    }
    return $_data;
}

/**
 * Adds a "stream_pay" field to Audio and Video create/update forms
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrProfileStreamPay_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (!isset($_user['quota_jrProfileStreamPay_allowed']) || $_user['quota_jrProfileStreamPay_allowed'] != 'on') {
        // Not active for this quota
        return $_data;
    }
    if (jrUser_is_admin()) {
        switch ($_post['module']) {

            case 'jrAudio':
            case 'jrVideo':
                list(, $view) = explode('/', $_data['form_view']);
                if ($view == 'create' || $view == 'update') {
                    $pfx = jrCore_db_get_prefix($_data['form_params']['module']);
                    if (isset($pfx) && strlen($pfx) > 0) {
                        $amnt = $_user["quota_jrProfileStreamPay_{$pfx}_amount"];
                        $_tmp = array(
                            'name'          => "{$pfx}_stream_amount",
                            'type'          => 'text',
                            'default'       => '',
                            'validate'      => 'price',
                            'min'           => '0.01',
                            'label'         => 'Stream Pay Amount',
                            'sublabel'      => "(default is: {$amnt} per stream)",
                            'help'          => "Enter a value here and it will override the default Quota {$pfx} stream amount (which is currently set to: {$amnt})",
                            'required'      => false,
                            'form_designer' => false // no form designer or we can't turn it off
                        );
                        jrCore_form_field_create($_tmp);
                    }
                }
                break;

        }
    }
    return $_data;
}

//----------------------------
// FUNCTIONS
//----------------------------

/**
 * Store a credit entry to the credit log
 * @param $module string Module
 * @param $item_id int Item ID
 * @param $name string Name of media item
 * @param $amount string Credit amount
 * @return bool
 */
function jrProfileStreamPay_log_credit_entry($module, $item_id, $name, $amount)
{
    global $_user;
    $tbl = jrCore_db_table_name('jrProfileStreamPay', 'log');
    $usr = (jrUser_is_logged_in()) ? intval($_user['_user_id']) : 0;
    $uip = jrCore_db_escape(jrCore_get_ip());
    $mod = jrCore_db_escape($module);
    $iid = (int) $item_id;
    $nam = jrCore_db_escape($name);
    $amt = jrCore_db_escape($amount);
    $req = "INSERT INTO {$tbl} (log_created, log_user_id, log_user_ip, log_module, log_item_id, log_item_name, log_amount)
            VALUES (UNIX_TIMESTAMP(), '{$usr}', '{$uip}', '{$mod}', '{$iid}', '{$nam}', '{$amt}')";
    $cnt = jrCore_db_query($req, 'INSERT_ID');
    if ($cnt && $cnt > 0) {
        return true;
    }
    return false;
}
