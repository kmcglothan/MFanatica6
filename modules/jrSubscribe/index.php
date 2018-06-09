<?php
/**
 * Jamroom Subscriptions module
 *
 * copyright 2018 The Jamroom Network
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

//-----------------------------------
// default
//-----------------------------------
function view_jrSubscribe_default($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscriptions");
}

//-----------------------------------
// update_payment_source
//-----------------------------------
function view_jrSubscribe_update_payment_source($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    $_ln = jrUser_load_lang_strings();
    jrCore_page_banner($_ln['jrSubscribe'][26]);
    jrCore_page_notice('success', $_ln['jrSubscribe'][32], false);
    jrCore_get_form_notice();
    $html = jrSubscribe_run_plugin_function('update_payment_source', $_post, $_user, $_conf);
    if ($html && strlen($html) > 0) {
        $plug = jrPayment_get_active_plugin();
        jrCore_page_custom($html, "<img src=\"{$_conf['jrCore_base_url']}/modules/jrPayment/img/{$plug}.png\" width=\"64\" height=\"64\" alt=\"" . jrCore_entity_string($_ln['jrSubscribe'][26]) . "\">");
    }
    jrCore_page_cancel_button('referrer');
    jrCore_page_display();
}

//-----------------------------------
// update_payment_source_save
//-----------------------------------
function view_jrSubscribe_update_payment_source_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrSubscribe_run_plugin_function('update_payment_source_save', $_post, $_user, $_conf);
    jrCore_location('referrer');
}

//------------------------------
// convert
//------------------------------
function view_jrSubscribe_convert($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe');

    jrCore_set_form_notice('success', "This tool will attempt to convert existing FoxyCart subscriptions to the selected Payment Processor");
    jrCore_page_banner('convert FoxyCart subscriptions');
    jrCore_get_form_notice();

    $_tmp = array(
        'submit_value'     => 'convert subscriptions',
        'cancel'           => 'referrer',
        'form_ajax_submit' => false,
        'submit_prompt'    => 'Convert FoxyCart Subscriptions?'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'convert_subscriptions',
        'label'    => 'convert subscriptions',
        'help'     => 'Check this option to convert the existing FoxyCart subscriptions to the selected Payment Processor',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'on',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // What plugins support our conversion?
    $_opt = array();
    foreach (jrPayment_get_plugins() as $plug => $title) {
        if ($plug != 'foxycart') {
            $func = "jrSubscribe_plugin_{$plug}_convert_foxycart_subscription";
            if (!function_exists($func)) {
                require_once APP_DIR . "/modules/jrSubscribe/plugins/{$plug}.php";
            }
            if (function_exists($func)) {
                $_opt[$plug] = $title;
            }
        }
    }

    $_tmp = array(
        'name'     => 'convert_plugin',
        'label'    => 'convert to',
        'help'     => 'Select the Payment Processor you want to convert the subscriptions to',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'not_empty',
        'default'  => 'stripe',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// convert_save
//------------------------------
function view_jrSubscribe_convert_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $cfg = jrPayment_get_plugin_config('foxycart');
    if (!$cfg || !is_array($cfg) || !isset($cfg['api_key']) || !isset($cfg['store_sub_domain'])) {
        jrCore_set_form_notice('error', "Unable to retrieve FoxyCart config - please ensure FoxyCart plugin has been configured");
        jrCore_form_result();
    }
    $cnt = 0;
    $bad = 0;

    // Get all local subscriptions that are foxycart
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "SELECT * FROM {$tbl} WHERE sub_plugin = 'foxycart'";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'There are no local subscriptions using FoxyCart');
        jrCore_form_result();
    }

    // Do we have any plans?
    if (!$_tm = jrSubscribe_get_all_plans(true)) {
        jrCore_set_form_notice('error', 'Unable to find any local subscription plans');
        jrCore_form_result();
    }

    $_pl = array();
    foreach ($_tm as $p) {
        $_pl["{$p['_item_id']}"] = $p;
    }
    unset($_tm);

    $plug = trim($_post['convert_plugin']);
    $func = "jrSubscribe_plugin_{$plug}_convert_foxycart_subscription";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrSubscribe/plugins/{$plug}.php";
    }
    if (!function_exists($func)) {
        jrCore_set_form_notice('error', "Unable to load required convert plugin function: {$func}");
        jrCore_form_result();
    }

    foreach ($_rt as $_sub) {
        if ($_pr = jrCore_db_get_item('jrProfile', $_sub['sub_profile_id'])) {
            if (isset($_pl["{$_sub['sub_plan_id']}"])) {
                if ($res = $func($_sub, $_pl["{$_sub['sub_plan_id']}"], $_pr)) {
                    if (strpos($res, 'ERROR:') === 0) {
                        jrCore_logger('MAJ', 'Subscribe: ' . substr($res, 7));
                        $bad++;
                    }
                    else {
                        $cnt++;
                    }
                }
                else {
                    $bad++;
                }
            }
        }
    }
    $_nt = array();
    if ($cnt > 0) {
        $_nt[] = jrCore_number_format($cnt) . ' profile subscriptions were successfully converted';
    }
    if ($bad > 0) {
        $_nt[] = jrCore_number_format($bad) . ' profiles subscriptions encountered errors during conversion and were not converted (see Activity Log)';
    }
    if (count($_nt) === 0) {
        $_nt[] = 'no profiles were found to convert';
    }
    jrCore_set_form_notice('notice', "<b>Conversion Results:</b><br><br>" . implode('<br>', $_nt), false);
    jrCore_form_result();
}

//------------------------------
// import
//------------------------------
function view_jrSubscribe_import($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe');

    jrCore_set_form_notice('success', "This tool will attempt to import existing FoxyCart subscribers in to the Subscription module");
    jrCore_page_banner('import FoxyCart subscriptions');
    jrCore_get_form_notice();

    $_tmp = array(
        'submit_value'     => 'import subscribers',
        'cancel'           => 'referrer',
        'form_ajax_submit' => false,
        'submit_prompt'    => 'Import Subscribers from FoxyCart?'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'create_subscriptions',
        'label'    => 'import subscriptions',
        'help'     => 'If this option is checked, active subscriber information will be imported from FoxyCart',
        'type'     => 'checkbox',
        'validate' => 'onoff',
        'default'  => 'on',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// import_save
//------------------------------
function view_jrSubscribe_import_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $cfg = jrPayment_get_plugin_config('foxycart');
    if (!$cfg || !is_array($cfg) || !isset($cfg['api_key']) || !isset($cfg['store_sub_domain'])) {
        jrCore_set_form_notice('error', "Unable to retrieve FoxyCart config - please ensure FoxyCart plugin has been configured");
        jrCore_form_result();
    }
    $cnt = 0;
    $bad = 0;
    $_rs = array(
        'api_token'        => $cfg['api_key'],
        'api_action'       => 'subscription_list',
        'is_active_filter' => '1'
    );
    $_rs = jrCore_load_url("https://{$cfg['store_sub_domain']}.foxycart.com/api", $_rs, 'POST', 443, null, null, false);
    if ($_rs) {
        $_rs = @simplexml_load_string($_rs, null, LIBXML_NOCDATA);
    }
    $_rs = json_decode(json_encode((array) $_rs), true);
    if ($_rs && is_array($_rs) && isset($_rs['subscriptions']['subscription']) && is_array($_rs['subscriptions']['subscription'])) {

        if (!isset($_rs['subscriptions']['subscription'][0])) {
            // There's only 1 subscription
            $_subscriptions = array($_rs['subscriptions']['subscription']);
        }
        else {
            $_subscriptions = $_rs['subscriptions']['subscription'];
        }

        $_sb = array();
        $_sp = array();
        foreach ($_subscriptions as $_sub) {
            if (isset($_sub['transaction_template']['custom_fields']['custom_field']) && is_array($_sub['transaction_template']['custom_fields']['custom_field'])) {

                $_fl = array();
                foreach ($_sub['transaction_template']['custom_fields']['custom_field'] as $k => $v) {
                    $_fl["{$v['custom_field_name']}"] = $v['custom_field_value'];
                }
                if (isset($_fl['profile_id']) && jrCore_checktype($_fl['profile_id'], 'number_nz')) {

                    $pid = (int) $_fl['profile_id'];

                    // Does this profile have a past-due amount?
                    // [past_due_amount] => 100.00

                    if (isset($_sub['past_due_amount']) && $_sub['past_due_amount'] > 0) {
                        // This subscription has expired - we need to get the CANCEL quota and make
                        // sure this profile is moved to the cancel quota
                        if ($_pr = jrCore_db_get_item('jrProfile', $pid)) {
                            if (isset($_pr['quota_jrFoxyCart_expire_quota']) && $_pr['profile_quota_id'] != $_pr['quota_jrFoxyCart_expire_quota']) {
                                $_up = array(
                                    'profile_quota_id' => (int) $_pr['quota_jrFoxyCart_expire_quota']
                                );
                                jrCore_db_update_item('jrProfile', $pid, $_up, null, false);
                                jrProfile_reset_cache($pid);
                                jrCore_logger('MIN', "Subscribe: profile_id {$pid} (@{$_pr['profile_url']}) moved to expire quota {$_pr['quota_jrFoxyCart_expire_quota']} for subscription failure", $_sub);
                            }
                            else {
                                jrCore_logger('MAJ', "Subscribe: unable to determine cancel quota_id for profile_id {$pid} (@{$_pr['profile_url']}) for subscription failure", $_sub);
                            }
                        }
                        continue;
                    }

                    // [next_transaction_date] => 2017-05-06
                    list($nyear, $nmonth, $nday) = explode('-', $_sub['next_transaction_date']);
                    list($syear, $smonth, $sday) = explode('-', $_sub['start_date']);

                    // [product_option_name] => quota_id
                    // [product_option_value] => 3
                    $quota_id = false;
                    if (isset($_sub['transaction_template']['transaction_details']['transaction_detail']['transaction_detail_options']['transaction_detail_option']['product_option_name']) &&
                        $_sub['transaction_template']['transaction_details']['transaction_detail']['transaction_detail_options']['transaction_detail_option']['product_option_name'] == 'quota_id'
                    ) {
                        $quota_id = (int) $_sub['transaction_template']['transaction_details']['transaction_detail']['transaction_detail_options']['transaction_detail_option']['product_option_value'];

                    }
                    else {
                        // Get quota_id from our plan_id
                        if (isset($_fl['plan_id'])) {
                            if ($_pl = jrCore_db_get_item('jrSubscribe', $_fl['plan_id'])) {
                                $quota_id = (int) $_pl['sub_quota_id'];
                            }
                        }
                        if (!$quota_id) {
                            if ($_pr = jrCore_db_get_item('jrProfile', $pid)) {
                                $quota_id = (int) $_pr['profile_quota_id'];
                            }
                            else {
                                // We can't even get this profile - skip
                                jrCore_logger('MIN', "Subscribe: unable to get profile info for profile_id {$pid} - skipping", $_sub);
                                continue;
                            }
                        }
                    }
                    $_sp[$pid] = array(
                        'quota_id'  => $quota_id,
                        'price'     => (int) ($_sub['transaction_template']['transaction_details']['transaction_detail']['product_price'] * 100),
                        'expires'   => mktime(23, 23, 59, $nmonth, $nday, $nyear),
                        'started'   => mktime(23, 23, 59, $smonth, $sday, $syear),
                        'frequency' => $_sub['frequency'],
                        'token'     => $_sub['sub_token']
                    );
                    $_sb[$pid] = $_sub;
                }
                else {
                    // We could not determine the profile_id in this subscription
                    jrCore_logger('CRI', "Subscribe: FoxyCart subscription does not contain a profile_id - skipping", $_sub);
                    $bad++;
                }
            }
            else {
                // With no custom fields we do not know what profile this subscription belongs to
                jrCore_logger('CRI', "Subscribe: FoxyCart subscription does not contain required custom fields - skipping", $_sub);
                $bad++;
            }
        }

        if (count($_sp) > 0) {

            // Get existing subscriptions
            $_es = array();
            $_ep = array(
                'skip_triggers' => true,
                'limit'         => 1000
            );
            $_ep = jrCore_db_search_items('jrSubscribe', $_ep);
            if ($_ep && is_array($_ep) && isset($_ep['_items'])) {
                foreach ($_ep['_items'] as $k => $s) {
                    $_es[$k]                 = $s;
                    $_es[$k]['sub_duration'] = trim(str_replace(':', '', $s['sub_duration']));
                }
            }

            $active = jrPayment_get_active_plugin();
            jrPayment_set_active_plugin('foxycart');
            foreach ($_sp as $pid => $_dat) {

                // Does this profile still exist?
                if ($_pr = jrCore_db_get_item('jrProfile', $pid)) {

                    // Do we have an import PLAN for this Quota ID?
                    $prc = 0;
                    $lid = false;
                    $qid = (int) $_dat['quota_id'];
                    $_pl = jrCore_db_get_item_by_key('jrSubscribe', 'sub_import_quota_id', $qid, true, true);
                    if (!$_pl || !is_array($_pl)) {
                        // We do not have an import plan created specifically for this quota
                        // See if we have one that matches the quota_id and frequency
                        if (count($_es) > 0) {
                            foreach ($_es as $k => $s) {
                                if ($s['sub_quota_id'] == $qid && $s['sub_duration'] == trim($_dat['frequency'])) {
                                    // We found the same sub
                                    $_pl = $_ep['_items'][$k];
                                    break;
                                }
                            }
                        }

                    }
                    if (!$_pl || !is_array($_pl)) {
                        // We have not created a SUBSCRIPTION PLAN for this quota yet
                        $tbl = jrCore_db_table_name('jrProfile', 'quota_value');
                        $req = "SELECT `name`, `value` FROM {$tbl} WHERE `module` = 'jrFoxyCart' AND quota_id = {$qid}";
                        $_rt = jrCore_db_query($req, 'name', false, 'value');
                        if ($_rt && is_array($_rt)) {
                            // Figure the subscription length
                            $tag = 'd';
                            if (strpos($_rt['subscription_length'], 'm')) {
                                $tag = 'm';
                            }
                            elseif (strpos($_rt['subscription_length'], 'y')) {
                                $tag = 'y';
                            }

                            // Is this subscription currently on a different price?
                            $prc = jrPayment_price_to_cents($_rt['subscription_price']);

                            // Get our Quota title
                            $req = "SELECT `name`, `value` FROM {$tbl} WHERE `module` = 'jrProfile' AND `name` = 'name' AND quota_id = {$qid}";
                            $_qn = jrCore_db_query($req, 'name', false, 'value');

                            $_cs = array(
                                'sub_title'            => (isset($_qn['name'])) ? $_qn['name'] : "Quota ID {$qid} (imported)",
                                'sub_trial'            => 0,
                                'sub_item_price'       => number_format($prc / 100, 2),
                                'sub_quota_id'         => $qid,
                                'sub_eot_quota_id'     => (int) $_rt['expire_quota'],
                                'sub_display_quota_id' => 0,
                                'sub_features'         => $_rt['subscription_desc'],
                                'sub_import_quota_id'  => $qid,
                                'sub_duration'         => intval($_rt['subscription_length']) . ":{$tag}",
                                'sub_active'           => 'on'
                            );
                            $lid = jrCore_db_create_item('jrSubscribe', $_cs);

                            // Run subscription_created functions for active plugin
                            jrPayment_set_active_plugin($active);
                            jrSubscribe_run_plugin_function('subscription_plan_created', $lid, $_cs);
                            jrPayment_set_active_plugin('foxycart');
                            $_cs['_item_id'] = $lid;
                        }
                    }
                    else {
                        $lid = $_pl['_item_id'];
                        $prc = jrPayment_price_to_cents($_pl['sub_item_price']);
                    }
                    if ($pid) {

                        if (isset($_post['create_subscriptions']) && $_post['create_subscriptions'] == 'on') {
                            if (isset($_dat['price']) && $_dat['price'] > 0) {
                                // We have a different price for this subscription
                                $prc = (int) $_dat['price'];
                            }
                            if (jrSubscribe_start_subscription($pid, $lid, $prc, $_dat['expires'], false, false, true, false, $_dat['started'], $_dat['expires'])) {
                                jrSubscribe_save_sub_metadata_key($pid, 'sub_token', $_dat['token']);
                                $cnt++;
                            }
                        }
                    }
                }
                else {
                    jrCore_logger('CRI', "Subscribe: FoxyCart profile_id {$pid} does not exist in the system - skipping", $_dat);
                    $bad++;
                }
            }
        }
    }
    $_nt = array();
    if ($cnt > 0) {
        $_nt[] = jrCore_number_format($cnt) . ' profile subscriptions were successfully imported';
    }
    if ($bad > 0) {
        $_nt[] = jrCore_number_format($bad) . ' profiles were not found in the system and were skipped (see Activity Log)';
    }
    if (count($_nt) === 0) {
        $_nt[] = 'no profiles were found to import in the subscription feed';
    }
    jrCore_set_form_notice('notice', "<b>Import Results:</b><br><br>" . implode('<br>', $_nt), false);
    jrCore_form_result();
}

//------------------------------
// default - site plan browser
//------------------------------
function view_jrSubscribe_plans($_post, $_user, $_conf)
{
    jrCore_get_form_notice();
    $_pl = jrSubscribe_get_all_plans(true, true);
    if (!$_pl || !is_array($_pl)) {
        jrCore_notice_page('error', 'There are no active subscriptions to subscribe to');
    }
    $_rp = array(
        'page_javascript' => '',
    );
    foreach ($_pl as $k => $_sub) {
        if (isset($_sub['_item_id']) && $_sub['_item_id'] > 0) {

            // Do we have any Javascript that should be included in the page?
            $inc = jrSubscribe_run_plugin_function('subscribe_javascript', $_sub);
            if ($inc && strlen($inc) > 0) {
                $_rp['page_javascript'] .= "\n{$inc}";
            }

            // See if we have an onclick function
            $onc = jrSubscribe_run_plugin_function('subscribe_onclick', $_sub);
            if ($onc && strlen($onc) > 0) {
                $_pl[$k]['sub_onclick'] = $onc;
            }
            else {
                // Check for URL function
                $url = jrSubscribe_run_plugin_function('subscribe_url', $_sub);
                if ($url && strlen($url) > 0) {
                    $_pl[$k]['sub_onclick'] = $onc;
                }
            }
        }
    }
    $_rp['_plans'] = $_pl;
    $html          = jrCore_parse_template('header.tpl', $_rp);
    $html          .= jrCore_parse_template('subscribe.tpl', $_rp, 'jrSubscribe');
    $html          .= jrCore_parse_template('footer.tpl', $_rp);
    return $html;
}

//------------------------------
// active (details on sub)
//------------------------------
function view_jrSubscribe_active_subscription($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    if (!$_es = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
        // This user does not have a subscription - is one pending?
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscriptions");
    }
    $_pl = jrCore_db_get_item('jrSubscribe', $_es['sub_plan_id'], true);
    $_ln = jrUser_load_lang_strings();

    if (isset($_post['_1'])) {
        switch ($_post['_1']) {
            case 'update':
                jrCore_set_form_notice('notice', $_ln['jrSubscribe'][20], false);
                jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription");
                break;
            case 'create':
                // Do we already have an active subscription?
                jrCore_set_form_notice('success', $_ln['jrSubscribe'][18], false);
                jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription");
                break;
        }
    }
    if (isset($_SESSION['jrsubscribe_sub_pending'])) {
        unset($_SESSION['jrsubscribe_sub_pending']);
    }

    $buttons = '';

    // We have an active subscription
    jrPayment_set_active_plugin($_es['sub_plugin']);

    // Do we have an update payment source onclick?
    $button1 = '';
    if ($_es['sub_status'] == 'free') {
        $button1 .= jrCore_page_button('update', $_ln['jrSubscribe'][26], 'disabled');
    }
    else {
        if ($update_onclick = jrSubscribe_run_plugin_function('subscribe_update_onclick', $_es)) {
            if ($_es['sub_status'] == 'canceled') {
                $button1 .= jrCore_page_button('update', $_ln['jrSubscribe'][26], 'disabled');
            }
            else {
                $button1 .= jrCore_page_button('update', $_ln['jrSubscribe'][26], $update_onclick);
            }
        }
    }

    // Do we have a cancel onclick?
    $button2 = '';
    if ($_es['sub_status'] == 'canceled') {
        $button2 .= jrCore_page_button('cancel', $_ln['jrSubscribe'][27], 'disabled');
    }
    elseif ($_es['sub_status'] == 'free') {
        // Allow a free subscription user to "cancel" their subscription
        $cancel_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_cancel_save";
        $button2    .= jrCore_page_button('cancel', $_ln['jrSubscribe'][27], "jrCore_confirm('" . addslashes($_ln['jrSubscribe'][15]) . "','" . addslashes($_ln['jrSubscribe'][16]) . "',function(){ jrCore_window_location('{$cancel_url}')})");
    }
    else {
        if ($cancel_onclick = jrSubscribe_run_plugin_function('subscribe_cancel_onclick', $_es)) {
            $button2 .= jrCore_page_button('cancel', $_ln['jrSubscribe'][15], $cancel_onclick);
        }
    }
    jrPayment_reset_active_plugin();

    // Show payment history
    $url = jrCore_get_module_url('jrPayment');
    if (jrUser_is_admin() && isset($_post['user_id']) && jrCore_checktype($_post['user_id'], 'number_nz')) {
        $buttons .= jrCore_page_button('history', $_ln['jrSubscribe'][49], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/purchases/user_id={$_post['user_id']}/item=jrSubscribe:{$_es['sub_plan_id']}')");
    }
    else {
        $buttons .= jrCore_page_button('history', $_ln['jrSubscribe'][49], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/purchases/item=jrSubscribe:{$_es['sub_plan_id']}')");
    }

    jrUser_account_tabs('subscriptions');
    jrCore_page_banner(1, $buttons);
    jrCore_get_form_notice();

    if ($_es['sub_status'] == 'canceled') {
        jrCore_page_notice('error', $_ln['jrSubscribe'][25] . jrCore_format_time($_es['sub_expires'], true));
    }
    elseif ($_es['sub_status'] == 'trial') {
        jrCore_page_notice('success', $_ln['jrSubscribe'][22] . jrCore_format_time($_es['sub_expires'], true));
    }

    $dat             = array();
    $dat[1]['title'] = $_ln['jrSubscribe'][2];
    $dat[1]['width'] = '10%';
    $dat[2]['title'] = $_ln['jrSubscribe'][39];
    $dat[2]['width'] = '30%';
    $dat[3]['title'] = $_ln['jrSubscribe'][40];
    $dat[3]['width'] = '15%';
    $dat[4]['title'] = $_ln['jrSubscribe'][41];
    $dat[4]['width'] = '15%';
    $dat[5]['title'] = $_ln['jrSubscribe'][42];
    $dat[5]['width'] = '15%';
    $dat[6]['title'] = $_ln['jrSubscribe'][43];
    $dat[6]['width'] = '15%';
    jrCore_page_table_header($dat);

    $dat = array();
    if (isset($_pl['sub_image_size']) && jrCore_checktype($_pl['sub_image_size'], 'number_nz')) {
        $_im             = array(
            'crop'   => 'auto',
            'width'  => 128,
            'height' => 128,
            'alt'    => $_pl['sub_title'],
            'title'  => $_pl['sub_title'],
            '_v'     => (isset($_pl['sub_image_time']) && $_pl['sub_image_time'] > 0) ? $_pl['sub_image_time'] : false
        );
        $dat[1]['title'] = jrImage_get_image_src('jrSubscribe', 'sub_image', $_pl['_item_id'], 'medium', $_im);
    }
    else {
        $dat[1]['title'] = jrCore_get_module_icon_html('jrSubscribe', 128, 'payment-icon');
    }
    $dat[2]['title'] = '<h3>' . $_pl['sub_title'] . '</h3>';
    $dat[2]['class'] = 'p10 center';

    switch ($_es['sub_status']) {
        case 'free':
        case 'active':
            $dat[3]['title'] = $_ln['jrSubscribe'][35];
            break;
        case 'trial':
            $dat[3]['title'] = $_ln['jrSubscribe'][36];
            break;
        case 'unpaid':
            $dat[3]['title'] = $_ln['jrSubscribe'][37];
            break;
        case 'canceled':
            $dat[3]['title'] = $_ln['jrSubscribe'][38];
            break;

    }
    $dat[3]['class'] = 'center';

    if ($_es['sub_status'] == 'trial') {
        $dat[4]['title'] = '<h3>' . jrPayment_get_currency_code() . jrPayment_currency_format($_pl['sub_item_price']) . '&nbsp;/&nbsp;' . jrSubscribe_get_text_duration($_pl['sub_duration']) . '<br><br>' . $_ln['jrSubscribe'][23] . '</h3>';
    }
    elseif ($_es['sub_status'] == 'canceled') {
        $dat[4]['title'] = '<h3>' . jrPayment_get_currency_code() . jrPayment_currency_format($_pl['sub_item_price']) . '&nbsp;/&nbsp;' . jrSubscribe_get_text_duration($_pl['sub_duration']) . '</h3>';
    }
    else {
        $dat[4]['title'] = '<h3>' . jrPayment_get_currency_code() . jrPayment_currency_format($_es['sub_amount']) . '&nbsp;/&nbsp;' . jrSubscribe_get_text_duration($_pl['sub_duration']) . '</h3>';
    }
    $dat[4]['class'] = 'center p10';
    $dat[5]['title'] = $button1;
    $dat[5]['class'] = 'center p10';
    $dat[6]['title'] = $button2;
    $dat[6]['class'] = 'center p10';
    jrCore_page_table_row($dat);
    jrCore_page_table_footer();

    // Do we have more than 1 subscription plan?
    if (jrCore_db_get_datastore_item_count('jrSubscribe') > 1 && $_es['sub_status'] != 'unpaid') {
        jrCore_page_cancel_button("{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscriptions", $_ln['jrSubscribe'][24]);
    }

    jrCore_page_display();
}

//------------------------------
// subscription_cancel_save (free)
//------------------------------
function view_jrSubscribe_subscription_cancel_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();
    $_sb = jrSubscribe_get_subscription_by_profile_id($_user['user_active_profile_id']);
    if (!$_sb || !is_array($_sb)) {
        jrCore_set_form_notice('error', 'invalid subscription id - data not found');
        jrCore_location('referrer');
    }
    if (jrSubscribe_delete_subscription($_sb['sub_profile_id'])) {
        jrCore_set_form_notice('success', 'The subscription was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'an error was encountered canceling the subscription - please try again');
    }
    jrCore_location('referrer');
}

//------------------------------
// subscriptions - logged in
//------------------------------
function view_jrSubscribe_subscriptions($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    $_ln = jrUser_load_lang_strings();
    $sym = jrPayment_get_currency_code();
    $_pl = jrSubscribe_get_all_plans(true, true);
    if (!$_pl || !is_array($_pl)) {
        jrCore_notice_page('error', $_ln['jrSubscribe'][34]);
    }
    $buttons = null;
    if (jrUser_is_master()) {
        $buttons = jrCore_page_button('modify', "admin: modify subscriptions", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse')");
    }
    jrUser_account_tabs('active_subscription');
    jrCore_page_banner(28, $buttons);

    if (isset($_SESSION['jrsubscribe_sub_pending'])) {
        jrCore_set_form_notice('success', "{$_ln['jrSubscribe'][18]}<br>{$_ln['jrSubscribe'][19]}", false);
    }
    jrCore_get_form_notice();

    // Does the viewing user have an active subscription?
    $_old   = false;
    $active = false;
    if ($_sb = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
        $active = $_sb['sub_plan_id'];
        $_old   = jrCore_db_get_item('jrSubscribe', $active);
    }

    $inc = '';
    $_pl = array_chunk($_pl, 4);
    foreach ($_pl as $_ch) {

        $num = count($_ch);
        $siz = '25%';
        if ($num < 4) {
            $siz = round(100 / $num, 1) . '%';
        }

        $dat = array();
        foreach ($_ch as $k => $_sub) {
            $dat[$k]['title'] = '<h2>' . $_sub['sub_title'] . '</h2>';
            $dat[$k]['width'] = $siz;
            if ($active && $_sub['_item_id'] == $active) {
                $dat[$k]['class'] = 'p10 success subscription-header';
            }
            else {
                $dat[$k]['class'] = 'p10 subscription-header';
            }
        }
        jrCore_page_table_header($dat);

        $dat = array();
        foreach ($_ch as $k => $_sub) {
            $dat[$k]['title'] = (isset($_sub['sub_features'])) ? trim($_sub['sub_features']) : '';
            $dat[$k]['class'] = 'p10 center subscription-features';
        }
        jrCore_page_table_row($dat);

        $dat = array();
        foreach ($_ch as $k => $_sub) {
            // Is this a variable priced subscription?
            if (isset($_sub['sub_variable']) && $_sub['sub_variable'] == 'on' && (!isset($active) || $_sub['_item_id'] != $active)) {
                $sub_price = $_sub['sub_item_price'];
                if ($tmp_price = jrCore_get_cookie('jr_subscribe_price')) {
                    $sub_price = $tmp_price;
                }
                $change_price_btn = jrCore_page_button("sub-price-{$_sub['_item_id']}", $sym . jrPayment_currency_format($sub_price), "jrSubscribe_set_sub_price({$_sub['_item_id']},'{$sym}')");
                $dat[$k]['title'] = "{$change_price_btn} &nbsp;<h3>/ " . jrSubscribe_get_sub_duration_string($_sub['sub_duration']) . '</h3>';
            }
            else {
                $price = $_sub['sub_item_price'];
                if (is_array($_sb) && $_sub['_item_id'] == $active) {
                    $price = (int) $_sb['sub_amount'];
                }
                $dat[$k]['title'] = "<h3>{$sym}" . jrPayment_currency_format($price) . ' / ' . jrSubscribe_get_sub_duration_string($_sub['sub_duration']) . '</h3>';
            }
            $dat[$k]['class'] = 'p10 center subscription-price';
        }
        jrCore_page_table_row($dat);

        $dat = array();
        foreach ($_ch as $k => $_sub) {

            if ($active && $_sub['_item_id'] == $active) {

                // Mark the users active subscription
                $dat[$k]['title'] = jrCore_page_button("subscribe-plan-{$k}", $_ln['jrSubscribe'][11], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription')");
                $dat[$k]['class'] = 'p10 center success';
            }
            elseif ($active && jrSubscriber_get_plugin_feature_flag('prorate_sub_change') !== 1) {

                // This plugin does not support prorating - we need to get the correct URL
                $onc = jrSubscribe_run_plugin_function('subscribe_change_onclick', $_sub, $_old, $_sb);
                if ($onc && strlen($onc) > 0) {
                    $dat[$k]['title'] = jrCore_page_button("subscribe-plan-{$k}", $_ln['jrSubscribe'][33], $onc);
                }
                else {
                    $url = jrSubscribe_run_plugin_function('subscribe_change_url', $_sub, $_old, $_sb);
                    if ($url && strlen($url) > 0) {
                        $dat[$k]['title'] = jrCore_page_button("subscribe-plan-{$k}", $_ln['jrSubscribe'][33], "jrCore_window_location('{$url}')");
                    }
                    else {
                        // handle manually
                        $dat[$k]['title'] = jrCore_page_button("subscribe-plan-{$k}", $_ln['jrSubscribe'][33], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/change/id={$_sub['_item_id']}')");
                    }
                }
                $dat[$k]['class'] = 'p10 center';
            }
            else {

                // We support upgrades and downgrades OR user is not a subscriber yet
                $pjs = jrSubscribe_run_plugin_function('subscribe_javascript', $_sub);
                if ($pjs && strlen($pjs) > 0) {
                    $inc .= "\n{$pjs}";
                }
                $onc = jrSubscribe_run_plugin_function('subscribe_onclick', $_sub);
                if ($onc && strlen($onc) > 0) {
                    $dat[$k]['title'] = jrCore_page_button("subscribe-plan-{$k}", $_ln['jrSubscribe'][33], $onc);
                }
                else {
                    $url = jrSubscribe_run_plugin_function('subscribe_url', $_sub);
                    if ($url && strlen($url) > 0) {
                        $dat[$k]['title'] = jrCore_page_button("subscribe-plan-{$k}", $_ln['jrSubscribe'][33], "jrCore_window_location('{$url}')");
                    }
                }
                $dat[$k]['class'] = 'p10 center';
            }
        }
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
    }

    // Do we have a variable price subscription?
    if (isset($change_price_btn)) {
        $htm = jrCore_parse_template('get_subscription_price.tpl', $_pl, 'jrSubscribe');
        jrCore_page_html($htm);
    }
    // Do we have any browser javascript elements?
    if ($js = jrSubscribe_run_plugin_function('browser_elements')) {
        jrCore_page_html($js);
    }
    if (strlen($inc) > 0) {
        jrCore_page_html($inc);
    }
    jrCore_page_display();
}

//------------------------------
// change
//------------------------------
function view_jrSubscribe_change($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Make sure we get a good plan
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid plan ID received - please try again');
        jrCore_location('referrer');
    }
    if (!$_pl = jrCore_db_get_item('jrSubscribe', $_post['id'])) {
        jrCore_set_form_notice('error', 'Invalid plan ID received - not found - please try again');
        jrCore_location('referrer');
    }
    if (!$_sb = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
        jrCore_set_form_notice('error', "Unable to retrieve existing subscription information - please try again");
        jrCore_location('referrer');
    }
    if (!$_op = jrCore_db_get_item('jrSubscribe', $_sb['sub_plan_id'])) {
        jrCore_set_form_notice('error', "Unable to retrieve existing subscription information - data not found - please try again");
        jrCore_location('referrer');
    }
    $_ln = jrUser_load_lang_strings();

    // Are we downgrading or upgrading?
    $old_price = (int) $_sb['sub_amount'];
    $new_price = jrPayment_price_to_cents($_pl['sub_item_price']);
    if ($old_price > $new_price) {
        // We are downgrading - if the user's subscription is NOT past_due,
        // we are going to add a profile_credit for the difference
        $amount = 0;
        $credit = 0;
        if ($_sb['sub_status'] != 'unpaid') {
            $credit = jrSubscribe_get_subscription_credit_amount($old_price, $_op['sub_duration'], $_sb['sub_expires']);
            if ($credit > 0) {
                if ($credit > $new_price) {
                    $amount = 0;
                }
                else {
                    $amount = ($new_price - $credit);
                }
            }
            else {
                $amount = $new_price;
            }
        }
    }
    elseif ($new_price > $old_price) {
        // We are UPGRADING - we need to figure out the amount due NOW
        if ($_sb['sub_status'] == 'active') {
            $credit = jrSubscribe_get_subscription_credit_amount($old_price, $_op['sub_duration'], $_sb['sub_expires']);
            $amount = ($new_price - $credit);
        }
        else {
            $credit = 0;
            $amount = $new_price;
        }
    }
    else {
        // Prices are the same - just change info to new subscription
        $credit = 0;
        $amount = 0;
    }

    jrUser_account_tabs('active_subscription');
    jrCore_page_banner('change subscription');
    $notice = "{$_ln['jrSubscribe'][44]} {$_op['sub_title']}<br>{$_ln['jrSubscribe'][45]} {$_pl['sub_title']}<br>";
    if ($credit > 0) {
        // Show the user the number of days they will be credited in the new plan
        $days = jrSubscribe_get_subscription_credit_days($_pl, $_op, $_sb['sub_expires']);
        if ($days > 0) {
            $notice .= "<br>{$_ln['jrSubscribe'][46]} <b>{$days} days</b>";
        }
    }

    $button = false;
    if ($amount > 0) {
        $onc = jrSubscribe_run_plugin_function('subscribe_onclick', $_pl, 'change');
        if ($onc && strlen($onc) > 0) {
            $button = jrCore_page_button("subscribe-plan-{$_pl['_item_id']}", $_ln['jrSubscribe'][47], $onc);
        }
        else {
            $url = jrSubscribe_run_plugin_function('subscribe_url', $_pl, 'change');
            if ($url && strlen($url) > 0) {
                $button = jrCore_page_button("subscribe-plan-{$_pl['_item_id']}", $_ln['jrSubscribe'][47], "jrCore_window_location('{$url}')");
            }
        }
    }
    else {
        // We have a credit - just need to change plans
        $onc = jrSubscribe_run_plugin_function('subscribe_change_onclick', $_pl, $_op, $_sb);
        if ($onc && strlen($onc) > 0) {
            $button = jrCore_page_button("subscribe-plan-{$_pl['_item_id']}", $_ln['jrSubscribe'][47], $onc);
        }
        else {
            $url = jrSubscribe_run_plugin_function('subscribe_change_url', $_pl, $_op, $_sb);
            if ($url && strlen($url) > 0) {
                $button = jrCore_page_button("subscribe-plan-{$_pl['_item_id']}", $_ln['jrSubscribe'][47], "jrCore_window_location('{$url}')");
            }
        }
        if (!$button) {
            $button = jrCore_page_button("subscribe-plan-{$_pl['_item_id']}", $_ln['jrSubscribe'][47], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/change_save/id={$_pl['_item_id']}')");
        }
    }
    if ($button) {
        $notice .= "<br><br>{$button}";
    }
    jrCore_page_notice('success', $notice, false);
    jrCore_page_display();
}

//------------------------------
// change_save
//------------------------------
function view_jrSubscribe_change_save($_post, $_user, $_conf)
{
    jrUser_session_require_login();

    // NOTE: We only make it here if the user is DOWNGRADING to a cheaper plan
    // and has credit on the existing subscription.  What we do for credit is
    // we basically push-off the start billing date to a number of days that
    // equals the credit amount on the new plan

    // Make sure we get a good plan
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid plan ID received - please try again');
        jrCore_location('referrer');
    }
    if (!$_pl = jrCore_db_get_item('jrSubscribe', $_post['id'])) {
        jrCore_set_form_notice('error', 'Invalid plan ID received - not found - please try again');
        jrCore_location('referrer');
    }
    if (!$_sb = jrSubscribe_get_profile_subscription($_user['user_active_profile_id'])) {
        jrCore_set_form_notice('error', "Unable to retrieve existing subscription information - please try again");
        jrCore_location('referrer');
    }
    if (!$_op = jrCore_db_get_item('jrSubscribe', $_sb['sub_plan_id'])) {
        jrCore_set_form_notice('error', "Unable to retrieve existing subscription information - data not found - please try again");
        jrCore_location('referrer');
    }

    // $_pl = plan we are changing TO
    // $_op = plan we are changing FROM
    $days = jrSubscribe_get_subscription_credit_days($_pl, $_op, $_sb['sub_expires']);

    // Run our subscription change
    $_new = array(
        'sub_plan_id' => $_pl['_item_id']
    );
    jrSubscribe_run_plugin_function('subscription_change_plan', $_sb, $_new, $days);

    // Make sure sub_expires is updated to properly reflect the credit days
    if ($days > 0) {
        jrSubscribe_update_subscription_field($_sb['sub_id'], 'sub_expires', (time() + ($days * 86400)));
        jrSubscribe_save_sub_metadata_key($_sb['sub_profile_id'], 'prorated', 1);
    }

    jrProfile_reset_cache($_user['user_active_profile_id']);
    jrUser_reset_cache($_user['_user_id']);

    $_ln = jrUser_load_lang_strings();
    jrCore_set_form_notice('success', "{$_ln['jrSubscribe'][18]}<br><br>{$_ln['jrSubscribe'][19]}", false);
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription");
}

//------------------------------
// success
//------------------------------
function view_jrSubscribe_success($_post, $_user, $_conf)
{
    jrCore_trigger_event('jrSubscribe', 'subscribe_success_page', $_post);
    $_ln = jrUser_load_lang_strings();
    if (!isset($_post['_1'])) {
        $_post['_1'] = 'create';
    }
    $note = "{$_ln['jrSubscribe'][18]}<br><br>{$_ln['jrSubscribe'][19]}";
    switch ($_post['_1']) {
        case 'update':
            $note = $_ln['jrSubscribe'][20];
            break;
        case 'cancel':
            $note = $_ln['jrSubscribe'][21];
            break;
    }
    $_SESSION['jrsubscribe_sub_pending'] = true;
    jrCore_notice_page('success', "<div class=\"p20 center\">{$note}</div>", "{$_conf['jrCore_base_url']}/{$_post['module_url']}/active_subscription", $_ln['jrCore'][87], false);
    jrCore_page_display();
}

//------------------------------
// plugin_view
//------------------------------
function view_jrSubscribe_plugin_view($_post, $_user, $_conf)
{
    // http://site.com/subscribe/plugin_view/plugin/?
    if (!isset($_post['_1'])) {
        return jrCore_notice_page('error', 'invalid plugin');
    }
    $func = "jrSubscribe_plugin_view_{$_post['_1']}_{$_post['_2']}";
    if (!function_exists($func)) {
        require_once APP_DIR . "/modules/jrPayment/plugins/{$_post['_1']}.php";
        require_once APP_DIR . "/modules/jrSubscribe/plugins/{$_post['_1']}.php";
    }
    if (function_exists($func)) {
        return $func($_post, $_user, $_conf);
    }
    return jrCore_notice_page('error', 'invalid plugin view');
}

//------------------------------
// subscribers
//------------------------------
function view_jrSubscribe_subscribers($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe', 'subscribers');

    $button = '';
    if (isset($_post['plan_id']) && jrCore_checktype($_post['plan_id'], 'number_nz')) {
        $button .= jrCore_page_button('all', "Show All Plans", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscribers')");
    }
    $button .= jrCore_page_button('create', "Create Subscription", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_create')");

    $url = jrCore_strip_url_params(jrCore_get_current_url(), array('type', 'p'));
    $button .= '<select name="sub_status" class="form_select form_select_item_jumper" onchange="jrCore_window_location(\'' . $url . "/type='+ $(this).val())\">\n";
    if (isset($_post['type']) && $_post['type'] == 'all') {
        $button .= "<option value=\"active\"> active subscriptions</option>\n";
        $button .= "<option value=\"all\" selected=\"selected\"> all subscriptions</option>\n";
    }
    else {
        $button .= "<option value=\"active\" selected=\"selected\"> active subscriptions</option>\n";
        $button .= "<option value=\"all\"> all subscriptions</option>\n";
    }
    $button .= '</select>';

    jrCore_page_banner('Subscribers', $button);
    jrCore_get_form_notice();
    jrCore_page_search('search', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscribers");

    $dat             = array();
    $dat[1]['title'] = 'IMG';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'profile name';
    $dat[2]['width'] = '25%';
    $dat[3]['title'] = 'subscription';
    $dat[3]['width'] = '25%';
    $dat[4]['title'] = 'expires / renews';
    $dat[4]['width'] = '20%';
    $dat[5]['title'] = 'amount';
    $dat[5]['width'] = '10%';
    $dat[6]['title'] = 'status';
    $dat[6]['width'] = '10%';
    $dat[7]['title'] = 'modify';
    $dat[7]['width'] = '5%';
    jrCore_page_table_header($dat);

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_sp = array();
        $_ss = array(
            'search'                       => array(
                "user_name like %{$_post['search_string']}% || user_email like %{$_post['search_string']}% || profile_name like %{$_post['search_string']}%"
            ),
            'return_keys'                  => array('_profile_id', '_item_id'),
            'exclude_jrProfile_quota_keys' => true,
            'ignore_pending'               => true,
            'limit'                        => 100
        );
        $_ss = jrCore_db_search_items('jrUser', $_ss);
        if ($_ss && is_array($_ss) && isset($_ss['_items'])) {
            foreach ($_ss['_items'] as $v) {
                $pid       = (int) $v['_profile_id'];
                $_sp[$pid] = $pid;
            }

        }
        if (count($_sp) > 0) {
            $req = "SELECT * FROM {$tbl} WHERE sub_profile_id IN(" . implode(',', $_sp) . ") ORDER BY sub_expires ASC";
        }
        else {
            // Set no match condition
            $req = "SELECT * FROM {$tbl} WHERE sub_id < 0";
        }
    }
    elseif (isset($_post['plan_id']) && jrCore_checktype($_post['plan_id'], 'number_nz')) {
        $pid = (int) $_post['plan_id'];
        if (isset($_post['type']) && $_post['type'] == 'all') {
            $req = "SELECT * FROM {$tbl} WHERE sub_plan_id = {$pid} ORDER BY sub_updated DESC";
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE sub_plan_id = {$pid} AND sub_status != 'inactive' ORDER BY sub_expires ASC";
        }
    }
    else {
        if (isset($_post['type']) && $_post['type'] == 'all') {
            $req = "SELECT * FROM {$tbl} ORDER BY sub_updated DESC";
        }
        else {
            $req = "SELECT * FROM {$tbl} WHERE sub_status != 'inactive' ORDER BY sub_expires ASC";
        }
    }
    $_rt = jrCore_db_paged_query($req, $page, 12, 'NUMERIC');

    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        $_pr = array();
        $_sb = array();
        $_tm = array();
        $_id = array();
        foreach ($_rt['_items'] as $k => $v) {
            $_id[] = (int) $v['sub_profile_id'];
            $_tm[] = (int) $v['sub_plan_id'];
        }
        if (count($_id) > 0) {
            $_sp = array(
                'search'         => array(
                    '_profile_id in ' . implode(',', $_id)
                ),
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true,
                'limit'          => count($_id)
            );
            $_sp = jrCore_db_search_items('jrProfile', $_sp);
            if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
                foreach ($_sp['_items'] as $k => $v) {
                    $pid       = (int) $v['_profile_id'];
                    $_pr[$pid] = $v;
                }
            }
        }
        if (count($_tm) > 0) {
            $_tm = jrCore_db_get_multiple_items('jrSubscribe', $_tm);
            if ($_tm && is_array($_tm)) {
                foreach ($_tm as $k => $v) {
                    $pid       = (int) $v['_item_id'];
                    $_sb[$pid] = $v;
                }
            }
        }

        $pass = jrCore_get_option_image('pass');
        $fail = jrCore_get_option_image('fail');
        foreach ($_rt['_items'] as $k => $_s) {

            $pid             = (int) $_s['sub_profile_id'];
            $dat             = array();
            $_im             = array(
                'crop'   => 'auto',
                'alt'    => (isset($_pr[$pid]['profile_name'])) ? '@' . $_pr[$pid]['profile_name'] : 'no profile image',
                'title'  => (isset($_pr[$pid]['profile_name'])) ? '@' . $_pr[$pid]['profile_name'] : 'no profile image',
                'width'  => 40,
                'height' => 40,
                '_v'     => (isset($_pr[$pid]['profile_image_time']) && $_pr[$pid]['profile_image_time'] > 0) ? $_pr[$pid]['profile_image_time'] : $_s['sub_updated']
            );
            $dat[1]['title'] = jrImage_get_image_src('jrProfile', "profile_image", $pid, 'small', $_im);
            $dat[2]['title'] = '<a href="' . $_conf['jrCore_base_url'] . '/' . $_pr[$pid]['profile_url'] . '" target="_blank">@' . $_pr[$pid]['profile_url'] . '</a><br><small>started: ' . jrCore_format_time($_s['sub_created'], true) . '</small>';
            $dat[3]['title'] = $_sb["{$_s['sub_plan_id']}"]['sub_title'];
            $dat[3]['class'] = 'center';

            $dat[4]['title'] = jrSubscriber_get_subscription_expire_date($_s['sub_expires']) . '<br><small>';
            if ($_s['sub_status'] == 'inactive') {
                $dat[4]['title'] .= 'expired';
            }
            elseif ($_s['sub_manual'] == '1' || $_s['sub_status'] == 'canceled') {
                $dat[4]['title'] .= 'expires';
            }
            else {
                $dat[4]['title'] .= 'renews';
            }
            $dat[4]['title'] .= '</small>';
            $dat[4]['class'] = 'center';

            $dat[5]['title'] = jrPayment_get_currency_code() . jrPayment_currency_format($_s['sub_amount']);
            $dat[5]['class'] = 'center';
            switch ($_s['sub_status']) {
                case 'active':
                    $dat[6]['title'] = $pass;
                    break;
                case 'inactive':
                    $dat[6]['title'] = $fail;
                    break;
                default:
                    $dat[6]['title'] = $_s['sub_status'];
                    break;
            }
            $dat[6]['class'] = 'center';
            $dat[7]['title'] = jrCore_page_button("modify-sub-{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_modify/id={$_s['sub_id']}')");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat = array();
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $dat[1]['title'] = "no active subscriptions found to match your search";
        }
        else {
            $dat[1]['title'] = "no active subscriptions found";
        }
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// subscription_create
//------------------------------
function view_jrSubscribe_subscription_create($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe', 'subscribers');
    jrCore_page_banner('create new subscription');

    // Form init
    $_tmp = array(
        'submit_value' => 'create subscription',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    $murl = jrCore_get_module_url('jrProfile');
    $_tmp = array(
        'name'      => 'link_profile_id',
        'label'     => 'profile name',
        'type'      => 'live_search',
        'help'      => 'Select the Profile you want to create a new subscription for',
        'validate'  => 'number_nz',
        'required'  => true,
        'error_msg' => 'You have selected an invalid Profile - please try again',
        'target'    => "{$_conf['jrCore_base_url']}/{$murl}/user_link_get_profile"
    );
    jrCore_form_field_create($_tmp);

    $_opt = array();
    $_tmp = jrSubscribe_get_all_plans();
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $p) {
            $_opt["{$p['_item_id']}"] = $p['sub_title'];
        }
    }
    $_tmp = array(
        'name'     => 'sub_plan_id',
        'label'    => 'plan',
        'help'     => 'Select the subscription plan',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_expires',
        'label'    => 'expires',
        'help'     => 'Select the date the subscription expires.<br><br><b>NOTE:</b> The subscription will be canceled during the daily maintenance cycle the night the subscription expires.',
        'type'     => 'date',
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// subscription_create_save
//------------------------------
function view_jrSubscribe_subscription_create_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $pid = 0;
    if (isset($_post['link_profile_id']) && jrCore_checktype($_post['link_profile_id'], 'number_nz')) {
        // We're good - they selected from the live search
        $pid = (int) $_post['link_profile_id'];
    }
    else {
        $_tm = jrCore_db_get_item_by_key('jrProfile', 'profile_name', $_post['link_profile_id'], true);
        if ($_tm && is_array($_tm)) {
            $pid = (int) $_tm['_profile_id'];
        }
        else {
            jrCore_set_form_notice('error', 'invalid profile name - please select a valid profile');
            jrCore_form_result();
        }
    }
    $sid = (int) $_post['sub_plan_id'];
    $exp = (int) $_post['sub_expires'];
    if (jrSubscribe_start_subscription($pid, $sid, 0, $exp, false, true, true, false, 0, $exp)) {
        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The subscription was successfully created');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscribers");
    }
    jrCore_set_form_notice('error', 'an error was encountered creating the subscription - please try again');
    jrCore_form_result();
}

//------------------------------
// subscription_modify
//------------------------------
function view_jrSubscribe_subscription_modify($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid subscription id');
        jrCore_location('referrer');
    }
    $_sb = jrSubscribe_get_subscription_by_id($_post['id']);
    if (!$_sb || !is_array($_sb)) {
        jrCore_set_form_notice('error', 'invalid subscription id - data not found');
        jrCore_location('referrer');
    }
    $_pr = jrCore_db_get_item('jrProfile', $_sb['sub_profile_id']);
    if (!$_pr || !is_array($_pr)) {
        jrCore_notice_page('error', "Profile defined in subscription does not exist");
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe', 'subscribers');

    jrPayment_set_active_plugin($_sb['sub_plugin']);

    $button = '';
    if ($_sb['sub_manual'] == 1 || jrSubscriber_get_plugin_feature_flag('delete_api_support') === 1) {
        $button = jrCore_page_button('subscriber-delete', 'delete subscription', "jrCore_confirm('Delete Subscription?', 'Are you sure you want to immediately delete this subscription?',function() { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscription_delete_save/id={$_post['id']}') })", array('class' => 'form_button sprite_icon_hilighted'));
    }
    elseif ($onclick = jrSubscribe_run_plugin_function('subscribe_delete_onclick', $_sb)) {
        $button = jrCore_page_button('subscriber-delete', 'delete subscription', $onclick, array('class' => 'form_button sprite_icon_hilighted'));
    }
    jrPayment_reset_active_plugin();
    jrCore_page_banner('modify subscription', $button);

    // Form init
    $_tmp = array(
        'submit_value' => 'update subscription',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => intval($_post['id'])
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_custom("&nbsp;<a href=\"{$_conf['jrCore_base_url']}/{$_pr['profile_url']}\" target=\"_blank\">@{$_pr['profile_url']}</a>", 'Profile');

    $_opt = array();
    $_tmp = jrSubscribe_get_all_plans();
    if ($_tmp && is_array($_tmp)) {
        foreach ($_tmp as $p) {
            if ($p['sub_active'] != 'on' && $p['_item_id'] != $_sb['sub_plan_id']) {
                continue;
            }
            $_opt["{$p['_item_id']}"] = $p['sub_title'];
        }
    }
    $_tmp = array(
        'name'     => 'sub_plan_id',
        'label'    => 'plan',
        'help'     => 'Select the subscription plan',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nz',
        'value'    => $_sb['sub_plan_id'],
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'free'     => array('Free', 'subscription is free and managed manually'),
        'active'   => array('Active', 'subscription is active and profile is making payments'),
        'trial'    => array('Trial', 'subscription is active and currently in the subscription plan trial period'),
        'unpaid'   => array('Unpaid', 'profile has overdue payment but subscription remains active during grace period'),
        'canceled' => array('Canceled', 'subscription has been canceled but profile remains in quota until end of term'),
        'inactive' => array('Inactive', 'subscription is no longer active')
    );
    $_opt = array();
    $_hlp = array();
    foreach ($_tmp as $k => $v) {
        $_opt[$k] = $v[0];
        $_hlp[$k] = "<b>{$v[0]}</b>: {$v[1]}";
    }
    $_tmp = array(
        'name'     => 'sub_status',
        'label'    => 'status',
        'help'     => 'Select the subscription status:<br><br>' . implode('<br>', $_hlp),
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'not_empty',
        'value'    => $_sb['sub_status'],
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_custom("&nbsp;" . jrCore_format_time($_sb['sub_created']), 'Started');

    $_tmp = array(
        'name'     => 'sub_expires',
        'label'    => 'expires',
        'help'     => 'Select the date the subscription expires.<br><br><b>NOTE:</b> The subscription will be canceled during the daily maintenance cycle the night the subscription expires.',
        'type'     => 'date',
        'validate' => 'number_nz',
        'value'    => $_sb['sub_expires'],
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Do we have custom sub_data?
    if (!empty($_sb['sub_data'])) {
        if ($_sd = json_decode($_sb['sub_data'], true)) {
            if (is_array($_sd)) {
                $html = '<div class="item"><table class="page_table" style="margin:0"><tbody>';
                $num  = 0;
                foreach ($_sd as $k => $v) {
                    $class = "page_table_row";
                    if (($num % 2) == 1) {
                        $class = "page_table_row_alt";
                    }
                    if ($k == '_updated') {
                        $v = jrCore_format_time($v);
                    }
                    $html .= '<tr class="' . $class . '"><td class="page_table_cell center"><b>' . $k . '</b></td><td class="page_table_cell">' . $v . '</td>';
                    $num++;
                }
                $html .= '</tbody></table></div>';
                jrCore_page_custom($html, 'Meta Data');
            }
        }
    }

    jrCore_page_display();
}

//------------------------------
// subscription_modify_save
//------------------------------
function view_jrSubscribe_subscription_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid subscription id');
        jrCore_location('referrer');
    }
    $sid = (int) $_post['id'];
    $_sb = jrSubscribe_get_subscription_by_id($sid);
    if (!$_sb || !is_array($_sb)) {
        jrCore_set_form_notice('error', 'invalid subscription id - data not found');
        jrCore_location('referrer');
    }
    $pid = (int) $_post['sub_plan_id'];
    $exp = (int) $_post['sub_expires'];

    // Let the plugin handle it's side of things if it can
    if ($pid != $_sb['sub_plan_id']) {
        // We are changing plans - is this a FREE subscription?
        if (isset($_sb['sub_status']) && $_sb['sub_status'] != 'free') {
            if ($_pl = jrCore_db_get_item('jrSubscribe', $pid, true, true)) {
                jrPayment_set_active_plugin($_sb['sub_plugin']);
                if (jrSubscribe_plugin_function_exists('subscription_change_plan')) {
                    if (!jrSubscribe_run_plugin_function('subscription_change_plan', $_sb, $_pl)) {
                        // We had an error in the plugin...
                        jrCore_form_result();
                    }
                }
                jrPayment_reset_active_plugin();
            }
        }
    }

    $sts = jrCore_db_escape($_post['sub_status']);
    $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
    $req = "UPDATE {$tbl} SET sub_plan_id = {$pid}, sub_status = '{$sts}', sub_updated = UNIX_TIMESTAMP(), sub_expires = {$exp} WHERE sub_id = {$sid} LIMIT 1";
    $cnt = jrCore_db_query($req, 'COUNT');
    if ($cnt && $cnt === 1) {
        jrCore_form_delete_session();
        jrProfile_reset_cache($_sb['sub_profile_id']);
        jrCore_logger('INF', "Subscribe: successfully updated subscription for profile_id {$_sb['sub_profile_id']}", $_post);
        jrCore_set_form_notice('success', 'The subscription was successfully updated');
    }
    else {
        jrCore_set_form_notice('error', 'an error was encountered updating the subscription - please try again');
    }
    jrCore_form_result();
}

//------------------------------
// subscription_delete_save
//------------------------------
function view_jrSubscribe_subscription_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid subscription id');
        jrCore_location('referrer');
    }
    $_sb = jrSubscribe_get_subscription_by_id($_post['id']);
    if (!$_sb || !is_array($_sb)) {
        jrCore_set_form_notice('error', 'invalid subscription id - data not found');
        jrCore_location('referrer');
    }

    // If this is a manually created subscription, do not run through API
    if ($_sb['sub_manual'] == 0) {
        jrPayment_set_active_plugin($_sb['sub_plugin']);
        if (jrSubscribe_plugin_function_exists('subscription_delete')) {
            if (!jrSubscribe_run_plugin_function('subscription_delete', $_sb)) {
                // We had an error in the plugin...
                jrCore_form_result();
            }
        }
        jrPayment_reset_active_plugin();
    }

    if (jrSubscribe_delete_subscription($_sb['sub_profile_id'])) {
        jrCore_set_form_notice('success', 'The subscription was successfully deleted');
        jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscribers");
    }
    jrCore_set_form_notice('error', 'an error was encountered deleting the subscription - please try again');
    jrCore_location('referrer');
}

//------------------------------
// browse
//------------------------------
function view_jrSubscribe_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe', 'browse');

    $button = jrCore_page_button('create', "Create New Subscription Plan", "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/plan_create')");
    jrCore_page_banner('Subscription Plans', $button);

    // If we are active with PayPal, we are limited by the 20% increase rule for subscriptions:
    if (jrPayment_get_active_plugin() == 'paypal') {
        jrCore_set_form_notice('notice', "PayPal subscriptions are limited to a maximum price increase of <b>20% every 180 days</b>.<br>This means the price difference between plans must be less than 20% or your users<br>will be unable to upgrade to a higher priced plan.<br>It is highly recommeded to only provide <b>One Subscription Plan</b> when using PayPal.", false);
    }
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'ID';
    $dat[1]['width'] = '5%';
    $dat[2]['title'] = 'plan name';
    $dat[2]['width'] = '45%';
    $dat[3]['title'] = 'price';
    $dat[3]['width'] = '8%';
    $dat[4]['title'] = 'trial';
    $dat[4]['width'] = '8%';
    $dat[5]['title'] = 'frequency';
    $dat[5]['width'] = '8%';
    $dat[6]['title'] = 'active';
    $dat[6]['width'] = '8%';
    $dat[7]['title'] = 'subscribers';
    $dat[7]['width'] = '8%';
    $dat[8]['title'] = 'modify';
    $dat[8]['width'] = '5%';
    $dat[9]['title'] = 'delete';
    $dat[9]['width'] = '5%';
    jrCore_page_table_header($dat);

    $page = 1;
    if (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) {
        $page = (int) $_post['p'];
    }

    $pass = jrCore_get_option_image('pass');
    $fail = jrCore_get_option_image('fail');

    $_rt = array(
        'order_by'      => array('_item_id' => 'asc'),
        'page'          => $page,
        'pagebreak'     => 12,
        'no_cache'      => true,
        'skip_triggers' => true
    );
    $_rt = jrCore_db_search_items('jrSubscribe', $_rt);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

        $tbl = jrCore_db_table_name('jrSubscribe', 'subscription');
        $req = "SELECT COUNT(sub_id) AS cnt, sub_plan_id FROM {$tbl} WHERE sub_status != 'inactive' GROUP BY sub_plan_id";
        $_ep = jrCore_db_query($req, 'sub_plan_id', false, 'cnt');

        foreach ($_rt['_items'] as $k => $_item) {
            $sid             = (int) $_item['_item_id'];
            $dat             = array();
            $dat[1]['title'] = $sid;
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_item['sub_title'];
            $dat[2]['class'] = 'center';
            if (isset($_item['sub_variable']) && $_item['sub_variable'] == 'on') {
                $dat[3]['title'] = '???';
            }
            else {
                $dat[3]['title'] = $_item['sub_item_price'];
            }
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrSubscribe_get_text_duration($_item['sub_trial'], true);
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = jrSubscribe_get_text_duration($_item['sub_duration'], true);
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = (isset($_item['sub_active']) && $_item['sub_active'] == 'on') ? $pass : $fail;
            $dat[6]['class'] = 'center';
            if (isset($_ep[$sid])) {
                $dat[7]['title'] = jrCore_page_button("browse-{$k}", jrCore_number_format($_ep[$sid]), "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/subscribers/plan_id={$sid}')");
            }
            else {
                $dat[7]['title'] = 0;
            }
            $dat[7]['class'] = 'center';
            $dat[8]['title'] = jrCore_page_button("modify-plan-{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/plan_modify/id={$sid}')");

            // We can't delete a subscription as long as there are subscribers
            if (isset($_ep[$sid])) {
                $dat[9]['title'] = jrCore_page_button("delete-plan-{$k}", 'delete', 'disabled');
            }
            else {
                $dat[9]['title'] = jrCore_page_button("delete-plan-{$k}", 'delete', "jrCore_confirm('Delete Plan?','Are you sure you want to delete this subscription plan?',function(){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/plan_delete_save/id={$sid}') })");
            }
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "no subscription plans have been created";
        $dat[1]['class'] = 'p10 center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    jrCore_page_display();
}

//------------------------------
// plan_create
//------------------------------
function view_jrSubscribe_plan_create($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe', 'browse');
    jrCore_page_banner('create new subscription plan');

    // Form init
    $_tmp = array(
        'submit_value' => 'create subscription plan',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_title',
        'label'    => 'title',
        'help'     => 'Enter the title of this new subscription',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_image',
        'label'    => 'image',
        'help'     => 'If an image is uploaded here it will be used as the plan image in the subscription browser',
        'text'     => 'select an image',
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_features',
        'label'    => 'features',
        'help'     => 'Enter the features this subscription offers',
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array_merge(array(0 => 'no free trial'), jrSubscribe_get_sub_durations());
    $_tmp = array(
        'name'     => 'sub_trial',
        'label'    => 'free trial',
        'help'     => 'If a free trail period is selected, payment will not be received for the subscription until the end of the trial period',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '0',
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    unset($_opt[0]);
    $_tmp = array(
        'name'     => 'sub_duration',
        'label'    => 'frequency',
        'help'     => 'When a user subscribes to this subscription, what is the interval between subscription payments?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '1:m',
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(
        0 => 'No grace period',
        1 => '1 day',
        2 => '2 days',
        3 => '3 days',
        4 => '4 days',
        5 => '5 days',
        6 => '6 days',
        7 => '7 days'
    );
    $_tmp = array(
        'name'     => 'sub_grace_period',
        'label'    => 'grace period',
        'help'     => 'When a users subscription has become UNPAID, how many days of grace period should be granted before canceling the subscription?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '1',
        'validate' => 'number_nn',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = jrProfile_get_quotas();
    $_tmp = array(
        'name'     => 'sub_quota_id',
        'label'    => 'subscription quota',
        'help'     => 'Select the Quota the user profile will be moved to when they successfully subscribe to this subscription',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array('0' => 'return to current Quota') + $_opt;
    $_tmp = array(
        'name'     => 'sub_eot_quota_id',
        'label'    => 'end of term quota',
        'help'     => 'Select the Quota the profile will be moved to when their subscription expires',
        'type'     => 'select',
        'options'  => $_opt,
        'value'    => 0,
        'validate' => 'number_nn',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(0 => 'All Quotas') + $_opt + array(999999 => 'No Quotas (hidden)');
    $_tmp = array(
        'name'     => 'sub_display_quota_id',
        'label'    => 'display quota',
        'help'     => 'If you would like this subscription plan to only be available to profiles that are in a specific quota, select it here',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nn',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_item_price',
        'label'    => 'price',
        'help'     => 'Enter the price of this subscription. If variable pricing is enabled for this subscription, this will be the default price that is shown when subscribing.',
        'type'     => 'text',
        'default'  => '',
        'min'      => '0.00',
        'validate' => 'price',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_variable',
        'label'    => 'variable price',
        'help'     => 'If this option is checked, the user will be allowed to enter the price they want to pay for the subscription.<br><br><b>NOTE:</b> A minimum subscription of ' . jrPayment_get_currency_code() . '1.00 is allowed for variable price subscriptions',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_active',
        'label'    => 'active',
        'help'     => 'If this option is unchecked, the subscription will not show in the subscription plan browser',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// plan_create_save
//------------------------------
function view_jrSubscribe_plan_create_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    $_cr = jrCore_form_get_save_data('jrSubscribe', 'plan_create', $_post);
    $lid = jrCore_db_create_item('jrSubscribe', $_cr);
    if ($lid && $lid > 0) {

        // Save sub image
        jrCore_save_all_media_files('jrSubscribe', 'plan_create', $_user['user_active_profile_id'], $lid);

        // Let modules know we have subscribed
        jrCore_trigger_event('jrSubscribe', 'subscription_plan_created', $_cr, array('_item_id' => $lid));

        // Run subscription_created functions for configured plugins
        jrSubscribe_run_plugin_function('subscription_plan_created', $lid, $_cr);

        jrCore_form_delete_session();
        jrCore_set_form_notice('success', 'The new subscription plan was successfully created');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");

    }
    jrCore_set_form_notice('error', 'an error was encountered creating the new subscription plan');
    jrCore_form_result();
}

//------------------------------
// plan_modify
//------------------------------
function view_jrSubscribe_plan_modify($_post, $_user, $_conf)
{
    jrUser_master_only();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid plan id');
        jrCore_location('referrer');
    }
    $_rt = jrCore_db_get_item('jrSubscribe', $_post['id'], false, true);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid plan id (2)');
        jrCore_location('referrer');
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSubscribe', 'browse');
    jrCore_page_banner('update subscription plan');

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => 'referrer',
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // id
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_title',
        'label'    => 'title',
        'help'     => 'Enter the title of this new subscription item',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_image',
        'label'    => 'image',
        'help'     => 'If an image is uploaded here it will be used as the plan image in the subscription browser',
        'text'     => 'select an image',
        'type'     => 'image',
        'value'    => $_rt,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_features',
        'label'    => 'features',
        'help'     => 'Enter the features this subscription offers',
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array_merge(array(0 => 'no free trial'), jrSubscribe_get_sub_durations());
    $_tmp = array(
        'name'     => 'sub_trial',
        'label'    => 'free trial',
        'help'     => 'If a free trail period is selected, payment will not be received for the subscription until the end of the trial period',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '0',
        'validate' => 'number_nz',
        'required' => true
    );
    if (jrPayment_get_active_plugin() == 'stripe') {
        $_tmp['disabled'] = 'disabled';
        $_tmp['sublabel'] = 'Changes locked by Stripe';
    }
    jrCore_form_field_create($_tmp);

    // If locked by Stripe
    if (jrPayment_get_active_plugin() == 'stripe') {
        $_tmp = array(
            'name'  => 'sub_trial',
            'type'  => 'hidden',
            'value' => $_rt['sub_trial']
        );
        jrCore_form_field_create($_tmp);
    }

    unset($_opt[0]);
    $_tmp = array(
        'name'     => 'sub_duration',
        'label'    => 'frequency',
        'help'     => 'When a user subscribes to this subscription, what is the interval between subscription payments?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '1m',
        'validate' => 'number_nz',
        'required' => true
    );
    if (jrPayment_get_active_plugin() == 'stripe') {
        $_tmp['disabled'] = 'disabled';
        $_tmp['sublabel'] = 'Changes locked by Stripe';
    }
    jrCore_form_field_create($_tmp);

    // If locked by Stripe
    if (jrPayment_get_active_plugin() == 'stripe') {
        $_tmp = array(
            'name'  => 'sub_duration',
            'type'  => 'hidden',
            'value' => $_rt['sub_duration']
        );
        jrCore_form_field_create($_tmp);
    }

    $_opt = array(
        0 => 'No grace period',
        1 => '1 day',
        2 => '2 days',
        3 => '3 days',
        4 => '4 days',
        5 => '5 days',
        6 => '6 days',
        7 => '7 days'
    );
    $_tmp = array(
        'name'     => 'sub_grace_period',
        'label'    => 'grace period',
        'help'     => 'When a users subscription has become UNPAID, how many days of grace period should be granted before canceling the subscription?',
        'type'     => 'select',
        'options'  => $_opt,
        'default'  => '1',
        'validate' => 'number_nn',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = jrProfile_get_quotas();
    $_tmp = array(
        'name'     => 'sub_quota_id',
        'label'    => 'subscription quota',
        'help'     => 'Select the Quota the user profile will be moved to when they successfully subscribe to this subscription',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nz',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array('0' => 'return to current Quota') + $_opt;
    $_tmp = array(
        'name'     => 'sub_eot_quota_id',
        'label'    => 'end of term quota',
        'help'     => 'Select the Quota the profile will be moved to when their subscription expires',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nn',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_opt = array(0 => 'All Quotas') + $_opt + array(999999 => 'No Quotas (hidden)');
    $_tmp = array(
        'name'     => 'sub_display_quota_id',
        'label'    => 'display quota',
        'help'     => 'If you would like this subscription plan to only be available to profiles that are in a specific quota, select it here',
        'type'     => 'select',
        'options'  => $_opt,
        'validate' => 'number_nn',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_item_price',
        'label'    => 'price',
        'help'     => 'Enter the price of this subscription. If variable pricing is enabled for this subscription, this will be the default price that is shown when subscribing.',
        'type'     => 'text',
        'default'  => '',
        'validate' => 'price',
        'required' => true
    );
    if (jrPayment_get_active_plugin() == 'stripe') {
        $_tmp['disabled'] = 'disabled';
        $_tmp['sublabel'] = 'Changes locked by Stripe';
    }
    jrCore_form_field_create($_tmp);

    // If locked by Stripe
    if (jrPayment_get_active_plugin() == 'stripe') {
        $_tmp = array(
            'name'  => 'sub_item_price',
            'type'  => 'hidden',
            'value' => $_rt['sub_item_price']
        );
        jrCore_form_field_create($_tmp);
    }

    $_tmp = array(
        'name'     => 'sub_variable',
        'label'    => 'variable price',
        'help'     => 'If this option is checked, the user will be allowed to enter the price they want to pay for the subscription<br><br><b>NOTE:</b> A minimum subscription of ' . jrPayment_get_currency_code() . '1.00 is allowed for variable price subscriptions',
        'type'     => 'checkbox',
        'default'  => 'off',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'sub_active',
        'label'    => 'active',
        'help'     => 'If this option is unchecked, the subscription will no longer show in the plan browser, yet will continue to be active for profiles that have already subscribed',
        'type'     => 'checkbox',
        'default'  => 'on',
        'validate' => 'onoff',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// plan_modify_save
//------------------------------
function view_jrSubscribe_plan_modify_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'invalid subscription id');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrSubscribe', $_post['id']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'invalid plan id');
        jrCore_form_result();
    }
    $_up = jrCore_form_get_save_data('jrSubscribe', 'plan_modify', $_post);
    if (jrCore_db_update_item('jrSubscribe', $_post['id'], $_up)) {

        // Save updated icon
        jrCore_save_all_media_files('jrSubscribe', 'plan_modify', $_rt['_profile_id'], $_post['id']);

        // Let modules know we have subscribed
        jrCore_trigger_event('jrSubscribe', 'subscription_plan_updated', $_rt, array('_item_id' => $_post['id']));

        // Run subscription_created functions for configured plugins
        jrSubscribe_run_plugin_function('subscription_plan_updated', $_post['id'], $_rt);

        jrCore_form_delete_session();
        jrUser_reset_cache($_user['_user_id'], 'jrSubscribe');
        jrCore_set_form_notice('success', 'The subscription plan was successfully updated');
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
    }
    jrCore_set_form_notice('error', 'an error was encountered updating the subscription plan');
    jrCore_form_result();
}

//------------------------------
// plan_delete_save
//------------------------------
function view_jrSubscribe_plan_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid plan id');
        jrCore_form_result('referrer');
    }
    if (jrSubscribe_delete_plan($_post['id'])) {
        jrCore_set_form_notice('success', 'The subscription plan was successfully deleted');
    }
    else {
        jrCore_set_form_notice('error', 'an error was encountered deleting the subscription plan');
    }
    jrCore_form_result('referrer');
}

//------------------------------
// check_price
//------------------------------
function view_jrSubscribe_check_price($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['price']) || !jrCore_checktype($_post['price'], 'price')) {
        $_ln = jrUser_load_lang_strings();
        jrCore_json_response(array('error' => $_ln['jrSubscribe'][55]));
    }
    $min = (isset($_conf['jrSubscribe_minimum_sub_amount'])) ? intval($_conf['jrSubscribe_minimum_sub_amount']) : 100;
    $prc = jrPayment_price_to_cents($_post['price']);
    if ($prc < $min) {
        $_ln = jrUser_load_lang_strings();
        $min = jrPayment_currency_format($min);
        $_er = array('error' => str_replace('%1', $min, $_ln['jrSubscribe'][56]));
        jrCore_json_response($_er);
    }
    jrCore_set_cookie('jr_subscribe_price', $prc);
    jrCore_json_response(array('OK' => 1));
}
