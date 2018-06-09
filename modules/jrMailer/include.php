<?php
/**
 * Jamroom Email Support module
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
function jrMailer_meta()
{
    $_tmp = array(
        'name'        => 'Email Support',
        'url'         => 'mailer',
        'version'     => '2.3.4',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Core support for Sending Email via an SMTP Server',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2864/email-core',
        'category'    => 'communication',
        'requires'    => 'jrCore:6.0.0',
        'recommended' => 'jrGeo',
        'priority'    => 1, // HIGHEST load priority
        'locked'      => true,
        'activate'    => true,
        'license'     => 'mpl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrMailer_init()
{
    // Register our JS
    jrCore_register_module_feature('jrCore', 'css', 'jrMailer', 'jrMailer.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMailer', 'jrMailer.js', 'admin');

    // Register our email plugin
    jrCore_register_system_plugin('jrMailer', 'email', 'smtp', 'SMTP Server configured in Delivery Settings (default)');

    // Test Email tab
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrMailer', 'test_email', 'Test Email');
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrMailer', 'browse', 'Stats Browser');
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMailer', 'browse', array('Stats Browser', 'Browse module email stats'));

    // Keep throttle table clean
    jrCore_register_event_listener('jrCore', 'daily_maintenance', 'jrMailer_daily_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'template_variables', 'jrMailer_template_variables_listener');
    jrCore_register_event_listener('jrCore', 'run_view_function', 'jrMailer_run_view_function_listener');
    jrCore_register_event_listener('jrCore', 'form_display', 'jrMailer_form_display_listener');
    jrCore_register_event_listener('jrCore', 'email_prepare', 'jrMailer_email_prepare_listener');
    jrCore_register_event_listener('jrCore', 'email_sent', 'jrMailer_email_sent_listener');
    jrCore_register_event_listener('jrCore', 'minute_maintenance', 'jrMailer_minute_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'hourly_maintenance', 'jrMailer_hourly_maintenance_listener');
    jrCore_register_event_listener('jrCore', 'system_check', 'jrMailer_system_check_listener');

    // Ensure our active campaign ID makes it through
    jrCore_register_event_listener('jrUser', 'notify_user', 'jrMailer_notify_user_listener');
    jrCore_register_event_listener('jrUser', 'site_privacy_check', 'jrMailer_site_privacy_check_listener');

    // We provide some newsletter filters
    jrCore_register_event_listener('jrNewsLetter', 'newsletter_filters', 'jrMailer_newsletter_filters_listener');

    // event triggers
    jrCore_register_event_trigger('jrMailer', 'gather_bounces', 'Fired hourly to gather bounced email');
    jrCore_register_event_trigger('jrMailer', 'process_bounces', 'Fired hourly with any bounced email addresses');
    jrCore_register_event_trigger('jrMailer', 'campaign_result_header', 'Fired in stats results for header row');
    jrCore_register_event_trigger('jrMailer', 'campaign_result_row', 'Fired in stats results for detail row');

    // Our Geo queue worker
    jrCore_register_queue_worker('jrMailer', 'process_stats', 'jrMailer_process_stats_worker', 0, 1, 170);
    return true;
}

//---------------------------------------
// NEWSLETTER RECIPIENTS
//---------------------------------------

/**
 * Get newsletter recipient email addresses
 * @param $id string Recipient function ID
 * @return array|bool
 */
function jrMailer_newsletter_filter($id)
{
    $_id = false;
    switch ($id) {
        case '1_plus_users':
            $_id = jrMailer_get_top_users(1);
            break;
        case '3_plus_users':
            $_id = jrMailer_get_top_users(3);
            break;
        case '5_plus_users':
            $_id = jrMailer_get_top_users(5);
            break;
        case 'viewed_users':
            $_id = jrMailer_get_viewed_users();
            break;
        default:
            // Is this a campaign?
            if (strpos($id, '_')) {
                list($cid, $nam) = explode('_', $id);
                $cid = (int) $cid;
                $nam = trim($nam);
                // Get users that were part of this campaign
                switch ($nam) {

                    case 'view':
                        // Users that viewed this newsletter
                        $tbl = jrCore_db_table_name('jrMailer', 'track');
                        $req = "SELECT t_uid AS u FROM {$tbl} WHERE t_cid = '{$cid}' AND t_unsub = 0";
                        $_id = jrCore_db_query($req, 'u', false, 'u');
                        break;

                    case 'click':
                        // Users that clicked a URL in the newsletter
                        $tbl = jrCore_db_table_name('jrMailer', 'click');
                        $req = "SELECT click_user_id AS u FROM {$tbl} WHERE click_campaign_id = '{$cid}'";
                        $_id = jrCore_db_query($req, 'u', false, 'u');
                        break;
                }

            }
            break;
    }
    if ($_id && is_array($_id) && count($_id) > 0) {
        return jrMailer_get_email_array_from_ids($_id);
    }
    return false;
}

//---------------------------------------
// QUEUE WORKERS
//---------------------------------------

/**
 * Process View Stats
 * @param $_queue array Queue entry
 * @return bool
 */
function jrMailer_process_stats_worker($_queue)
{
    // Update Geo Location if enabled
    if (jrCore_module_is_active('jrGeo')) {

        $pid = $_queue['pid'];
        $tbl = jrCore_db_table_name('jrMailer', 'track');
        $req = "UPDATE {$tbl} SET t_lat = '_pid_{$pid}' WHERE t_ip != '' AND t_lat = '' LIMIT 1000";
        $cnt = jrCore_db_query($req, 'COUNT');
        if ($cnt && $cnt > 0) {

            // We have rows to update
            $req = "SELECT t_id, t_cid, t_uid, t_ip, t_agent FROM {$tbl} WHERE t_lat = '_pid_{$pid}'";
            $_rt = jrCore_db_query($req, 'NUMERIC');
            if ($_rt && is_array($_rt)) {
                foreach ($_rt as $s) {

                    // Get Agent info
                    $inf = '';
                    if (strlen($s['t_agent']) > 0) {
                        $_in = jrMailer_get_browser_info($s['t_agent']);
                        if ($_in && is_array($_in)) {
                            $inf = jrCore_db_escape(json_encode($_in));
                        }
                    }

                    // Get IP Info
                    $lat = '';
                    $lng = '';
                    $cny = '';
                    $rgn = '';
                    $cty = '';
                    if (strlen($s['t_ip']) > 0) {
                        $_ip = jrGeo_location($s['t_ip']);
                        if ($_ip && is_array($_ip) && isset($_ip['latitude'])) {
                            $lat = jrCore_db_escape($_ip['latitude']);
                            $lng = jrCore_db_escape($_ip['longitude']);
                            $cny = jrCore_db_escape(jrCore_strip_emoji($_ip['country_name'], false));
                            $rgn = jrCore_db_escape(jrCore_strip_emoji($_ip['region'], false));
                            $cty = jrCore_db_escape(jrCore_strip_emoji($_ip['city'], false));
                        }
                    }

                    // Update
                    $req = "UPDATE {$tbl} SET t_time = UNIX_TIMESTAMP(), t_lat = '{$lat}', t_long = '{$lng}', t_country = '{$cny}', t_region = '{$rgn}', t_city = '{$cty}', t_agent = '{$inf}' WHERE t_id = {$s['t_id']}";
                    jrCore_db_query($req);
                }
            }
        }
    }
    return true;
}

//-----------------------------------
// EVENT LISTENERS
//-----------------------------------

/**
 * Newsletter Filters
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_newsletter_filters_listener($_data, $_user, $_conf, $_args, $event)
{
    $_data['jrMailer'] = array(
        '1_plus_users' => 'Top Users (clicked on 1+ Newsletter URLs)',
        '3_plus_users' => 'Top Users (clicked on 3+ Newsletter URLs)',
        '5_plus_users' => 'Top Users (clicked on 5+ Newsletter URLs)',
        'viewed_users' => 'Viewed Users (opened a previous Newsletter)'
    );
    // We also provide filters for previous newsletter sends
    $tbl = jrCore_db_table_name('jrMailer', 'campaign');
    $req = "SELECT c_id, c_title FROM {$tbl} ORDER BY c_updated DESC";
    $_rt = jrCore_db_query($req, 'c_id', false, 'c_title');
    if ($_rt && is_array($_rt)) {
        foreach ($_rt as $cid => $title) {
            $_data['jrMailer']["{$cid}_view"]  = "NewsLetter: {$title} (user viewed)";
            $_data['jrMailer']["{$cid}_click"] = "NewsLetter: {$title} (user clicked URL)";
        }
    }
    return $_data;
}

/**
 * Process stats
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_minute_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // NOTE: We only ever have 1 queue entry active at any time for process_stats
    if (jrCore_module_is_active('jrGeo')) {
        jrCore_queue_create('jrMailer', 'process_stats', array('pid' => getmypid()), 0, null, 1);
    }
    return $_data;
}

/**
 * Allow tracking even if site is private
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_site_privacy_check_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['module']) && $_args['module'] == 'jrMailer' && isset($_args['option'])) {
        switch ($_args['option']) {
            case 'l':
            case 'link':
                $_data['allow_private_site_view'] = true;
                break;
        }
    }
    return $_data;
}

/**
 * Insert active campaign ID into global space when notifying users
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_notify_user_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_args['_options']['campaign_id']) && jrCore_checktype($_args['_options']['campaign_id'], 'number_nz')) {
        jrMailer_set_active_campaign_id($_args['_options']['campaign_id']);
    }
    return $_data;
}

/**
 * Send event for bounced email listeners
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_hourly_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    // Gather bounces
    $_bounces = jrCore_trigger_event('jrMailer', 'gather_bounces', array());
    if ($_bounces && is_array($_bounces)) {

        foreach ($_bounces as $address => $_info) {

            // Increment any campaign bounces
            if (isset($_info['subject']) && strlen(trim($_info['subject'])) > 0) {
                $tbl = jrCore_db_table_name('jrMailer', 'campaign');
                $req = "UPDATE {$tbl} SET c_bounce = (c_bounce + 1) WHERE c_title = '" . jrCore_db_escape(trim($_info['subject'])) . "' LIMIT 1";
                jrCore_db_query($req);
            }

            // Unsubscribe user
            $_us = jrCore_db_get_item_by_key('jrUser', 'user_email', $address, true);
            if ($_us && is_array($_us) && isset($_us['_user_id'])) {
                jrCore_db_update_item('jrUser', $_us['_user_id'], array('user_notifications_disabled' => 'on'));
            }

        }
        jrCore_trigger_event('jrMailer', 'process_bounces', $_bounces);
    }

    // Makes sure any hung Geo workers are reset
    $tbl = jrCore_db_table_name('jrMailer', 'track');
    $req = "UPDATE {$tbl} SET t_lat = '' WHERE t_lat LIKE '_pid_%' AND t_time < (UNIX_TIMESTAMP() - 3600)";
    jrCore_db_query($req);

    return $_data;
}

/**
 * Parse outgoing email for campaign work
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_email_prepare_listener($_data, $_user, $_conf, $_args, $event)
{

    // Are we running a campaign?
    if (isset($_data['_options']['campaign_id']) && jrCore_checktype($_data['_options']['campaign_id'], 'number_nz')) {

        // We are doing a campaign - get campaign info
        $cid = (int) $_data['_options']['campaign_id'];
        if (!$_cp = jrCore_get_flag("jrmailer_campaign_info_{$cid}")) {
            $_cp = jrMailer_get_campaign_info_by_id($cid);
            if (!$_cp || !is_array($_cp)) {
                $_cp = 'not_found';
            }
            jrCore_set_flag("jrmailer_campaign_info_{$cid}", $_cp);
        }
        if ($_cp && is_array($_cp)) {

            // Set our active campaign ID
            jrMailer_set_active_campaign_id($_data['_options']['campaign_id']);

            // There are URLs in this message that need to be remapped
            jrCore_set_flag('jrMailer_unmapped_message', $_data['_options']['message']);
            $_data['_options']['message'] = jrMailer_map_campaign_urls($cid, $_data['_options']['message']);

            // We have a campaign ID - insert tracking
            $_id = array();
            foreach ($_data['address'] as $address) {
                $_id[] = $address;
            }
            $_sc = array(
                'search'         => array(
                    'user_email in ' . implode(',', $_id)
                ),
                'return_keys'    => array('_user_id', 'user_email'),
                'skip_triggers'  => true,
                'ignore_pending' => true,
                'privacy_check'  => false,
                'limit'          => count($_id)
            );
            $_sc = jrCore_db_search_items('jrUser', $_sc);
            $_id = array();
            if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
                $_data['_user_ids'] = array();
                foreach ($_sc['_items'] as $_u) {
                    $_id["{$_u['user_email']}"] = (int) $_u['_user_id'];
                    $_data['_user_ids'][]       = (int) $_u['_user_id'];
                }
            }
            if (count($_id) > 0) {
                jrCore_set_flag('jrMailer_tracked_user_ids', $_id);
            }

            $_tr = array();
            $url = jrCore_get_module_url('jrMailer');
            foreach ($_data['address'] as $address) {
                if (isset($_id[$address]) && $_id[$address] > 0) {
                    $_tr[$address] = "{$_conf['jrCore_base_url']}/{$url}/i/{$cid}/" . intval($_id[$address]) . '/link.gif';
                }
            }
            if (count($_tr) > 0) {
                jrCore_set_flag('jrMailer_tracked_addresses', $_tr);
            }
        }
    }
    return $_data;
}

/**
 * Mark email as sent
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_email_sent_listener($_data, $_user, $_conf, $_args, $event)
{
    // Increment the number of emails sent for this campaign
    if (isset($_data['total_sent']) && $_data['total_sent'] > 0 && isset($_data['_options']['campaign_id'])) {
        jrMailer_increment_campaign_count($_data['_options']['campaign_id'], 'sent', $_data['total_sent']);
        // Increment campaign counts for the users
        if (isset($_data['_user_ids']) && is_array($_data['_user_ids'])) {
            foreach ($_data['_user_ids'] as $id) {
                // Increment campaign counters for this batch of users
                jrCore_db_increment_key('jrUser', $id, 'user_campaign_total_sent', 1);
            }
        }
    }
    return $_data;
}

/**
 * Keep throttle table cleaned up
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_daily_maintenance_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_conf['jrMailer_throttle']) && jrCore_checktype($_conf['jrMailer_throttle'], 'number_nz')) {
        $min = strftime('%y%m%d%H%M');
        $tbl = jrCore_db_table_name('jrMailer', 'throttle');
        $req = "SELECT t_min FROM {$tbl} WHERE t_min < {$min}";
        $_rt = jrCore_db_query($req, 't_min');
        if ($_rt && is_array($_rt) && count($_rt) > 0) {
            $req = "DELETE FROM {$tbl} WHERE t_min IN(" . implode(',', array_keys($_rt)) . ')';
            jrCore_db_query($req);
        }
    }
    return $_data;
}

/**
 * Add campaign ID to unsubscribe footer
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_template_variables_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['jr_template']) && ($_data['jr_template'] == 'email_preferences_footer.tpl' || $_data['jr_template'] == 'email_preferences_html_footer.tpl')) {
        if ($cid = jrMailer_get_active_campaign_id()) {
            // We have an active campaign - add to unsubscribe URL so we can track unsubscribes
            $md5 = md5($cid);
            if (strpos($_data['unsubscribe_url'], '?r=1')) {
                $_data['unsubscribe_url'] = jrCore_strip_url_params($_data['unsubscribe_url'], array('r'));
                $_data['unsubscribe_url'] = "{$_data['unsubscribe_url']}/r=1/cid={$md5}";
            }
            else {
                $_data['unsubscribe_url'] = "{$_data['unsubscribe_url']}/cid={$md5}";
            }

            if (strpos($_data['preferences_url'], '?r=1')) {
                $_data['preferences_url'] = jrCore_strip_url_params($_data['preferences_url'], array('r'));
                $_data['preferences_url'] = "{$_data['preferences_url']}/r=1/cid={$md5}";
            }
            else {
                $_data['preferences_url'] = "{$_data['preferences_url']}/cid={$md5}";
            }
        }
    }
    return $_data;
}

/**
 * Add hidden Campaign ID to unsubscribe forms
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_post['cid']) && jrCore_checktype($_post['cid'], 'md5')) {

        // It appears this user is unsubscribing due to a campaign
        switch ($_data['form_view']) {

            case 'jrUser/unsubscribe':

                // Users who are NOT logged in will stay on the unsubscribe form
                if (!jrUser_is_logged_in() && isset($_post['_1']) && jrCore_checktype($_post['_1'], 'md5')) {
                    $_rt = jrMailer_get_campaign_info_by_md5($_post['cid']);
                    if ($_rt && is_array($_rt)) {

                        // We have a good campaign
                        $_tmp = array(
                            'name'  => 'cid',
                            'type'  => 'hidden',
                            'value' => $_post['cid']
                        );
                        jrCore_form_field_create($_tmp);

                        // We know they viewed the email
                        $_us = jrCore_db_get_item_by_key('jrUser', 'user_validate', $_post['_1'], true);
                        if ($_us && is_array($_us)) {
                            jrMailer_track_campaign_view($_us['_user_id'], $_rt['c_id']);
                        }
                    }
                }
                break;

            case 'jrUser/notifications':

                // If we have a campaign ID in our session, user has come to unsubscribe
                if (jrUser_is_logged_in()) {
                    $_tmp = array(
                        'name'  => 'cid',
                        'type'  => 'hidden',
                        'value' => $_post['cid']
                    );
                    jrCore_form_field_create($_tmp);
                }
                break;

        }
    }
    return $_data;
}

/**
 * Add hidden Campaign ID to unsubscribe forms
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_run_view_function_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_post['cid']) && jrCore_checktype($_post['cid'], 'md5')) {

        if (isset($_post['module']) && $_post['module'] == 'jrUser' && isset($_post['option']{1})) {

            switch ($_post['option']) {

                case 'unsubscribe':
                    if (jrUser_is_logged_in()) {
                        // Logged in users are going to be redirect to their notification preferences
                        // Intercept that here so we can pass a long our campaign ID

                        // We know they SAW this campaign if they have clicked on unsubscribe
                        $_rt = jrMailer_get_campaign_info_by_md5($_post['cid']);
                        if ($_rt && is_array($_rt)) {

                            // This campaign is still alive - track
                            jrMailer_track_campaign_view($_user['_user_id'], $_rt['c_id']);

                            $url = jrCore_get_module_url('jrUser');
                            $pid = jrUser_get_profile_home_key('_profile_id');
                            jrCore_location("{$_conf['jrCore_base_url']}/{$url}/notifications/profile_id={$pid}/user_id={$_user['_user_id']}/cid={$_post['cid']}");
                        }
                    }
                    break;

                case 'unsubscribe_save':

                    if (!jrUser_is_logged_in()) {
                        // A non logged in user has chosen to unsubscribe
                        if (isset($_post['user_validate']) && jrCore_checktype($_post['user_validate'], 'md5')) {
                            $_us = jrCore_db_get_item_by_key('jrUser', 'user_validate', $_post['user_validate'], true);
                            if ($_us && is_array($_us)) {

                                // We have a valid user - do we have a valid campaign?
                                $_rt = jrMailer_get_campaign_info_by_md5($_post['cid']);
                                if ($_rt && is_array($_rt)) {

                                    // Yep - count our unsubscribe as long as we didn't already
                                    $cnt = jrMailer_set_unsubscribe_flag($_us['_user_id'], $_rt['c_id']);
                                    if ($cnt && $cnt === 1) {

                                        // First time unsubscribing from this campaign
                                        $tbl = jrCore_db_table_name('jrMailer', 'campaign');
                                        $req = "UPDATE {$tbl} SET c_unsub = (c_unsub + 1) WHERE c_id = '{$_rt['c_id']}' LIMIT 1";
                                        $cnt = jrCore_db_query($req, 'COUNT');
                                        if (!$cnt || $cnt !== 1) {
                                            // This is a campaign that does not exist
                                            jrCore_logger('MAJ', "unable to increment unsubscribe count for Campaign ID");
                                        }
                                        else {
                                            // Update tracking row to show they unsubscribed from THIS campaign
                                            $tbl = jrCore_db_table_name('jrMailer', 'track');
                                            $req = "UPDATE {$tbl} SET t_unsub = 1 WHERE t_cid = '{$_rt['c_id']}' AND t_uid = '{$_us['_user_id']}'";
                                            jrCore_db_query($req);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'notifications':

                    // We know they SAW this campaign
                    if (jrUser_is_logged_in()) {
                        $_rt = jrMailer_get_campaign_info_by_md5($_post['cid']);
                        if ($_rt && is_array($_rt)) {

                            // This campaign is still alive - track
                            jrMailer_track_campaign_view($_user['_user_id'], $_rt['c_id']);

                        }
                    }
                    break;

                case 'notifications_save':

                    // Is this user unsubscribing after following an unsubscribe URL from a campaign?
                    if (jrUser_is_logged_in()) {

                        $unsub = false;
                        if (isset($_post['user_notifications_disabled']) && $_post['user_notifications_disabled'] == 'on') {
                            // This user has turned off ALL notifications
                            $unsub = true;
                        }
                        if (!isset($_user['user_jrNewsLetter_notifications']) || $_user['user_jrNewsletter_notifications'] == 'email') {
                            // This user WAS subscribing - are they turning it off?
                            if (isset($_post['event_jrNewsLetter_newsletter']) && $_post['event_jrNewsLetter_newsletter'] != 'email') {
                                // Yes - they unsubscribed
                                $unsub = true;
                            }
                        }
                        if ($unsub) {
                            $_rt = jrMailer_get_campaign_info_by_md5($_post['cid']);
                            if ($_rt && is_array($_rt)) {

                                // count our unsubscribe - as long as we didn't already
                                $cnt = jrMailer_set_unsubscribe_flag($_user['_user_id'], $_rt['c_id']);
                                if ($cnt && $cnt === 1) {

                                    // First time unsubscribing from this campaign
                                    $tbl = jrCore_db_table_name('jrMailer', 'campaign');
                                    $req = "UPDATE {$tbl} SET c_unsub = (c_unsub + 1) WHERE c_id = '{$_rt['c_id']}' LIMIT 1";
                                    $cnt = jrCore_db_query($req, 'COUNT');
                                    if (!$cnt || $cnt !== 1) {
                                        // This is a campaign that does no exist
                                        jrCore_logger('MAJ', "unable to increment unsubscribe count for Campaign ID");
                                    }
                                    else {
                                        // Update tracking row to show they unsubscribed from THIS campaign
                                        $tbl = jrCore_db_table_name('jrMailer', 'track');
                                        $req = "UPDATE {$tbl} SET t_unsub = 1 WHERE t_cid = '{$_rt['c_id']}' AND t_uid = '{$_user['_user_id']}'";
                                        jrCore_db_query($req);
                                    }
                                }
                            }
                        }
                    }
                    unset($_data['cid']);
                    break;

            }
        }
    }
    return $_data;
}

/**
 * Make sure GeoIP module is installed and active
 * @param array $_data incoming data array
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrMailer_system_check_listener($_data, $_user, $_conf, $_args, $event)
{
    // Check for GeoIP module
    $dat             = array();
    $dat[1]['title'] = 'Mail Geo Location';
    $dat[1]['class'] = 'center';
    $dat[2]['title'] = 'active';
    $dat[2]['class'] = 'center';

    if (jrCore_module_is_active('jrGeo')) {
        $dat[3]['title'] = $_args['pass'];
        $dat[4]['title'] = 'Geo Location module is active and usable by Mail Support';
    }
    else {
        $mmurl           = jrCore_get_module_url('jrMarket');
        $dat[3]['title'] = $_args['fail'];
        $dat[4]['title'] = 'Install the <a href="' . $_conf['jrCore_base_url'] . '/' . $mmurl . '/browse/module?search_string=jrGeo" style="text-decoration:underline">Geo Location</a> module for better email stats';
    }
    $dat[3]['class'] = 'center';
    jrCore_page_table_row($dat);
    return $_data;
}

//-----------------------------------
// FUNCTIONS
//-----------------------------------

/**
 * Get the adjusted country name for the Google Geo map
 * @param string $country
 * @return bool|string
 */
function jrMailer_get_country_name_for_map($country)
{
    switch ($country) {
        case 'Russian Federation':
            return 'Russia';
            break;
        default:
            if (strpos($country, ',')) {
                return substr($country, 0, strpos($country, ','));
            }
            break;
    }
    return $country;
}

/**
 * Return TRUE if viewer is a "real" viewer
 * @return bool
 */
function jrMailer_is_real_user_agent()
{
    if (strlen($_SERVER['HTTP_USER_AGENT']) > 0) {
        $_bad = array(
            'proxy',
            'appengine',
            'virus',
            'bot',
            'spider',
            'crawl'
        );
        foreach ($_bad as $bad) {
            if (stripos(' ' . $_SERVER['HTTP_USER_AGENT'], $bad)) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Get capabilities of browser
 * @param $agent
 * @return mixed
 */
function jrMailer_get_browser_info($agent)
{
    if (!function_exists('parse_user_agent')) {
        require_once APP_DIR . '/modules/jrMailer/contrib/PhpUserAgent/Source/UserAgentParser.php';
    }
    if ($_rt = parse_user_agent($agent)) {
        if (is_null($_rt['platform']) && stripos(' ' . $agent, 'windows')) {
            $_rt['platform'] = 'Windows';
        }
        return $_rt;
    }
    return false;
}

/**
 * Get an email array from a given array if user ids
 * @param $_ids array
 * @return array|bool
 */
function jrMailer_get_email_array_from_ids($_ids)
{
    if (is_array($_ids) && count($_ids) > 0) {
        $_tm = jrCore_db_get_multiple_items('jrUser', $_ids, array('_user_id', 'user_email'));
        if ($_tm && is_array($_tm)) {
            $_us = array();
            foreach ($_tm as $_u) {
                $uid       = (int) $_u['_user_id'];
                $_us[$uid] = $_u['user_email'];
            }
            if (count($_us) > 0) {
                return $_us;
            }
        }
    }
    return false;
}

/**
 * marketplace browser tabs
 * @param $active
 * @return bool
 */
function jrMailer_campaign_tabs($active)
{
    global $_conf, $_post;
    $_tbs                    = array(
        "view"  => array(
            'label' => 'Viewed',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/campaign_result/{$_post['_1']}"
        ),
        "urls"  => array(
            'label' => 'Clicked URLs',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/campaign_result/{$_post['_1']}/urls"
        ),
        "map"   => array(
            'label' => 'World Map',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/campaign_result/{$_post['_1']}/map"
        ),
        "unsub" => array(
            'label' => 'Unsubscribes',
            'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/campaign_result/{$_post['_1']}/unsub"
        ),
    );
    $_tbs[$active]['active'] = 1;
    jrCore_page_tab_bar($_tbs);
    return true;
}

/**
 * Delete a campaign from the DB
 * @param $campaign_id int Campaign ID
 * @return bool
 */
function jrMailer_delete_campaign($campaign_id)
{
    $cid = (int) $campaign_id;

    // Delete from campaigns table
    $tbl = jrCore_db_table_name('jrMailer', 'campaign');
    $req = "DELETE FROM {$tbl} WHERE c_id = '{$cid}'";
    jrCore_db_query($req);

    // Delete Clicks
    $tbl = jrCore_db_table_name('jrMailer', 'click');
    $req = "DELETE FROM {$tbl} WHERE click_campaign_id = '{$cid}'";
    jrCore_db_query($req);

    // Delete Tracks
    $tbl = jrCore_db_table_name('jrMailer', 'track');
    $req = "DELETE FROM {$tbl} WHERE t_cid = '{$cid}'";
    jrCore_db_query($req);

    // Delete Un subscribes
    $tbl = jrCore_db_table_name('jrMailer', 'unsubscribe');
    $req = "DELETE FROM {$tbl} WHERE u_cid = '{$cid}'";
    jrCore_db_query($req);

    // NOTE: We do not delete from URLs - this way future clicks
    // on a tracked URL still work and forward correctly

    return true;
}

/**
 * Get all Campaigns
 * @return mixed
 */
function jrMailer_get_all_campaigns()
{
    $tbl = jrCore_db_table_name('jrMailer', 'campaign');
    $req = "SELECT * FROM {$tbl} ORDER BY c_created DESC";
    return jrCore_db_query($req, 'c_id');
}

/**
 * Get all user IDs that have viewed a campaign email
 * @return array|bool
 */
function jrMailer_get_viewed_users()
{
    $tbl = jrCore_db_table_name('jrMailer', 'track');
    $req = "SELECT t_uid FROM {$tbl} GROUP BY t_uid";
    $_rt = jrCore_db_query($req, 't_uid', false, 't_uid');
    if ($_rt && is_array($_rt)) {
        return array_keys($_rt);
    }
    return false;
}

/**
 * Get users that have unsubscribed during a campaign
 * @param $campaign_id int Campaign ID
 * @return bool|mixed
 */
function jrMailer_get_unsubscribed_users($campaign_id)
{
    $cid = (int) $campaign_id;
    $tbl = jrCore_db_table_name('jrMailer', 'unsubscribe');
    $req = "SELECT u_uid, u_time FROM {$tbl} WHERE u_cid = '{$cid}' ORDER BY u_time DESC";
    $_rt = jrCore_db_query($req, 'u_uid', false, 'u_time');
    if ($_rt && is_array($_rt)) {
        $_sc = array(
            'search'                 => array(
                '_item_id in ' . implode(',', array_keys($_rt))
            ),
            'return_keys'            => array('_user_id', '_updated', 'user_image_time', 'user_name', 'user_email', 'profile_url'),
            'include_jrProfile_keys' => true,
            'ignore_pending'         => true,
            'privacy_check'          => false,
            'limit'                  => count($_rt)
        );
        $_sc = jrCore_db_search_items('jrUser', $_sc);
        if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
            foreach ($_sc['_items'] as $k => $_u) {
                $uid = (int) $_u['_user_id'];
                if (isset($_rt[$uid])) {
                    $_sc['_items'][$k]['u_time'] = $_rt[$uid];
                }
            }
            return $_sc['_items'];
        }
    }
    return false;
}

/**
 * Get "top" users - users who have clicked on at least 1 Campaign URL
 * @param $top_count int Number of clicks to include as top users
 * @return array|bool
 */
function jrMailer_get_top_users($top_count = 1)
{
    $tbl = jrCore_db_table_name('jrMailer', 'click');
    if ($top_count > 1) {
        $req = "SELECT COUNT(click_id) AS cnt, click_user_id FROM {$tbl} GROUP BY click_user_id HAVING COUNT(click_id) >= {$top_count}";
    }
    else {
        $req = "SELECT click_user_id FROM {$tbl} GROUP BY click_user_id";
    }
    $_rt = jrCore_db_query($req, 'click_user_id', false, 'click_user_id');
    if ($_rt && is_array($_rt)) {
        return array_keys($_rt);
    }
    return false;
}

/**
 * Get the active campaign ID during an email send
 * @return mixed
 */
function jrMailer_get_active_campaign_id()
{
    return jrCore_get_flag('jrmailer_active_campaign_id');
}

/**
 * Set the active campaign ID during an email send
 * @param $id int Campaign ID
 * @return bool
 */
function jrMailer_set_active_campaign_id($id)
{
    return jrCore_set_flag('jrmailer_active_campaign_id', intval($id));
}

/**
 * Get a User Report
 * @param $user_id int User ID
 * @param $campaign_id int Campaign ID
 * @return array|bool|mixed
 */
function jrMailer_get_user_report($user_id, $campaign_id)
{
    global $_conf;
    // Get user info
    $uid = (int) $user_id;
    $cid = (int) $campaign_id;
    $_us = jrCore_db_get_item('jrUser', $user_id);
    if (!$_us || !is_array($_us)) {
        return false;
    }
    $_pr = jrCore_db_get_item('jrProfile', $_us['_profile_id'], true);
    if ($_pr && is_array($_pr)) {
        $_us = array_merge($_us, $_pr);
    }
    unset($_pr);

    // Get campaign Views
    $tbl = jrCore_db_table_name('jrMailer', 'track');
    $req = "SELECT *, COUNT(t_id) AS cnt FROM {$tbl} WHERE t_uid = '{$uid}' AND t_cid = '{$cid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        $_us['total_campaign_views'] = (int) $_rt['cnt'];
        $_ul                         = array();
        foreach (array('t_city', 't_region', 't_country') as $t) {
            if (strlen($_rt[$t]) > 0) {
                if ($t == 't_region' && is_numeric($_rt[$t])) {
                    continue;
                }
                $_ul[] = $_rt[$t];
            }
        }
        if (count($_ul) > 0) {
            $_us['latest_campaign_location'] = implode(', ', $_ul);
        }
        else {
            $_us['latest_campaign_location'] = 'unknown';
        }
        $_us['user_latitude']  = $_rt['t_lat'];
        $_us['user_longitude'] = $_rt['t_long'];
        $_us['user_city']      = $_rt['t_city'];
        $_us['user_region']    = $_rt['t_region'];
        $_us['user_country']   = $_rt['t_county'];

        // Do we have info on this user's browser?
        $platform = 'unknown';
        $browser  = 'unknown';
        $version  = 'unknown';
        if (strlen($_rt['t_info']) > 0) {
            $_rt['t_info'] = json_decode($_rt['t_info'], true);
            if ($_rt['t_info'] && is_array($_rt['t_info'])) {
                $platform = (isset($_rt['t_info']['platform']) && $_rt['t_info']['platform'] != 'null') ? $_rt['t_info']['platform'] : 'unknown';
                $browser  = (isset($_rt['t_info']['browser']) && $_rt['t_info']['browser'] != 'null') ? $_rt['t_info']['browser'] : 'unknown';
                $version  = (isset($_rt['t_info']['version']) && $_rt['t_info']['version'] != 'null') ? $_rt['t_info']['version'] : 'unknown';
            }
        }
        $_us['user_platform'] = $platform;
        $_us['user_browser']  = $browser;
        $_us['user_version']  = $version;
    }

    // Get campaign Clicks
    $tbl = jrCore_db_table_name('jrMailer', 'click');
    $req = "SELECT COUNT(click_id) AS cnt FROM {$tbl} WHERE click_user_id = '{$uid}' AND click_campaign_id = '{$cid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        $_us['total_campaign_clicks'] = (int) $_rt['cnt'];
    }

    // Get campaign URLs
    $tb1 = jrCore_db_table_name('jrMailer', 'click');
    $tb2 = jrCore_db_table_name('jrMailer', 'url');
    $req = "SELECT COUNT(c.click_id) AS cnt, u.url_uri FROM {$tb1} c LEFT JOIN {$tb2} u ON u.url_id = c.click_url_id
             WHERE c.click_user_id = '{$uid}' AND c.click_campaign_id = '{$cid}'
             GROUP BY c.click_url_id ORDER BY cnt DESC";
    $_rt = jrCore_db_query($req, 'url_uri', false, 'cnt');
    if ($_rt && is_array($_rt)) {
        $_us['_urls'] = array();
        foreach ($_rt as $url => $cnt) {
            $url                = str_replace($_conf['jrCore_base_url'], '', $url);
            $_us['_urls'][$url] = $cnt;
        }
    }
    return $_us;
}

/**
 * Get results for an email campaign
 * @param $_campaign_ids array Array of Campaign IDs
 * @param $skip_cache bool set to TRUE to skip cache
 * @param $limit int Number of results
 * @return array|bool
 */
function jrMailer_get_campaign_results($_campaign_ids, $skip_cache = false, $limit = 100)
{
    global $_conf;
    if (!is_array($_campaign_ids)) {
        return false;
    }
    $key = md5(json_encode($_campaign_ids));
    if ($skip_cache || !$_cm = jrCore_is_cached('jrMailer', $key)) {
        $_cm = array();
        $_in = array();
        foreach ($_campaign_ids as $cid) {
            $cid = (int) $cid;
            if ($cid > 0) {
                $_in[$cid] = (int) $cid;
            }
            else {
                $_in[$cid] = false;
            }
        }

        // Get Campaign data
        $tbl = jrCore_db_table_name('jrMailer', 'campaign');
        $req = "SELECT * FROM {$tbl} WHERE c_id IN(" . implode(',', $_in) . ")";
        $_cp = jrCore_db_query($req, 'c_id');
        if (!$_cp || !is_array($_cp)) {
            foreach ($_in as $cid => $res) {
                $_cm[$cid] = false;
            }
            return $_cm;
        }
        else {
            foreach ($_cp as $cid => $_c) {
                $_cm[$cid] = array('campaign' => $_c);
            }
        }

        // Get Tracking Stats
        $tbl = jrCore_db_table_name('jrMailer', 'track');
        $req = "SELECT t_cid, COUNT(t_id) AS total FROM {$tbl} WHERE t_cid IN(" . implode(',', $_in) . ") GROUP BY t_cid";
        $_st = jrCore_db_query($req, 't_cid');
        if ($_st && is_array($_st)) {
            foreach ($_st as $cid => $_c) {
                $_cm[$cid]['results'] = $_c;
            }
        }

        // Get Views
        $_vu = array();
        $req = "SELECT t_cid, t_uid, t_time FROM {$tbl} WHERE t_cid IN(" . implode(',', $_in) . ") GROUP BY t_uid ORDER BY t_time DESC LIMIT {$limit}";
        $_st = jrCore_db_query($req, 't_uid');
        if ($_st && is_array($_st)) {
            foreach ($_st as $_c) {
                $cid = (int) $_c['t_cid'];
                if (!isset($_cm[$cid]['views'])) {
                    $_cm[$cid]['views'] = array();
                }
                $uid                      = (int) $_c['t_uid'];
                $_cm[$cid]['views'][$uid] = $_c;
                $_vu[$uid]                = $uid;
            }
            if (count($_vu) > 0) {
                $_sc = array(
                    'search'                 => array(
                        '_item_id in ' . implode(',', $_vu)
                    ),
                    'return_keys'            => array('_user_id', '_updated', 'user_image_time', 'user_name', 'user_email', 'profile_url'),
                    'include_jrProfile_keys' => true,
                    'ignore_pending'         => true,
                    'privacy_check'          => false,
                    'limit'                  => count($_vu)
                );
                $_sc = jrCore_db_search_items('jrUser', $_sc);
                if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
                    $_ui = array();
                    foreach ($_sc['_items'] as $_u) {
                        $uid       = (int) $_u['_user_id'];
                        $_ui[$uid] = $_u;
                    }
                    foreach ($_cm as $cid => $_i) {
                        if (isset($_i['views'])) {
                            foreach ($_i['views'] as $uid => $v) {
                                if (isset($_ui[$uid])) {
                                    $_cm[$cid]['views'][$uid] = array_merge($v, $_ui[$uid]);
                                }
                                else {
                                    unset($_cm[$cid]['views'][$uid]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Get unique Countries by Count
        $req = "SELECT t_cid, t_country, COUNT(t_id) AS total FROM {$tbl} WHERE t_cid IN(" . implode(',', $_in) . ") GROUP BY t_cid, t_country";
        $_st = jrCore_db_query($req, 't_country');
        if ($_st && is_array($_st)) {
            foreach ($_st as $_c) {
                $cid = (int) $_c['t_cid'];
                if (strlen($_c['t_country']) > 0) {
                    $_cm[$cid]['countries']["{$_c['t_country']}"] = $_c['total'];
                }
                else {
                    $_cm[$cid]['countries']['Unknown'] = $_c['total'];
                }
            }
        }

        // Total Clicks
        $tbl = jrCore_db_table_name('jrMailer', 'click');
        $req = "SELECT SUM(click_count) as tc, click_campaign_id FROM {$tbl} WHERE click_campaign_id IN(" . implode(',', $_in) . ") GROUP BY click_campaign_id";
        $_rt = jrCore_db_query($req, 'click_campaign_id', false, 'tc');
        if ($_rt && is_array($_rt)) {
            foreach ($_rt as $cid => $cnt) {
                if (!isset($_cm[$cid]['results'])) {
                    $_cm[$cid]['results'] = array();
                }
                $_cm[$cid]['results']['clicks'] = $cnt;
            }
        }

        // Get TOP URLs
        $tb1 = jrCore_db_table_name('jrMailer', 'click');
        $tb2 = jrCore_db_table_name('jrMailer', 'url');
        $req = "SELECT * FROM {$tb2} u LEFT JOIN {$tb1} c ON c.click_url_id = u.url_id WHERE c.click_campaign_id IN(" . implode(',', $_in) . ") ORDER BY c.click_time DESC LIMIT {$limit}";
        $_st = jrCore_db_query($req, 'NUMERIC');
        if ($_st && is_array($_st)) {
            foreach ($_st as $_c) {

                // Initialize campaign counters
                $cid = (int) $_c['click_campaign_id'];
                if (!isset($_cm[$cid]['urls'])) {
                    $_cm[$cid]['urls'] = array();
                    $_cm[$cid]['uids'] = array();
                }

                // URL Counts
                $lid = (int) $_c['click_id'];
                if (isset($_cm[$cid]['urls'][$lid])) {
                    $_cm[$cid]['urls'][$lid]['count'] += $_c['click_count'];
                }
                else {
                    $_cm[$cid]['urls'][$lid] = array(
                        'url'   => trim(str_replace($_conf['jrCore_base_url'], '', $_c['url_uri'])),
                        'uid'   => (int) $_c['click_user_id'],
                        'time'  => (int) $_c['click_time'],
                        'count' => $_c['click_count']
                    );
                    if (strlen($_cm[$cid]['urls'][$lid]['url']) === 0) {
                        $_cm[$cid]['urls'][$lid]['url'] = '/';
                    }
                }
                $uid                     = (int) $_c['click_user_id'];
                $_cm[$cid]['uids'][$uid] = $uid;

            }

            foreach ($_cm as $cid => $_inf) {
                if (isset($_cm[$cid]['uids'])) {
                    $total = count($_cm[$cid]['uids']);
                    if ($total > 0) {
                        $_sc = array(
                            'search'                 => array(
                                '_item_id in ' . implode(',', $_cm[$cid]['uids'])
                            ),
                            'return_keys'            => array('_user_id', '_updated', 'user_image_time', 'user_name', 'user_email', 'profile_url'),
                            'include_jrProfile_keys' => true,
                            'ignore_pending'         => true,
                            'privacy_check'          => false,
                            'limit'                  => count($_cm[$cid]['uids'])
                        );
                        $_sc = jrCore_db_search_items('jrUser', $_sc);
                        if ($_sc && is_array($_sc) && isset($_sc['_items'])) {
                            $_ui = array();
                            foreach ($_sc['_items'] as $_u) {
                                $uid       = (int) $_u['_user_id'];
                                $_ui[$uid] = $_u;
                            }
                            foreach ($_cm[$cid]['urls'] as $lid => $_i) {
                                $uid = (int) $_i['uid'];
                                if (isset($_ui[$uid])) {
                                    $_cm[$cid]['urls'][$lid] = array_merge($_i, $_ui[$uid]);
                                    unset($_cm[$cid]['urls'][$lid]['uid']);
                                }
                                else {
                                    unset($_cm[$cid]['urls'][$lid]);
                                }
                            }
                        }
                    }
                }
            }
        }
        jrCore_add_to_cache('jrMailer', $key, $_cm, 10);
    }
    return $_cm;
}

/**
 * Get a campaign URL by unique URL ID
 * @param $url_id int URL ID
 * @return bool
 */
function jrMailer_get_campaign_url_by_id($url_id)
{
    $uid = (int) $url_id;
    $tbl = jrCore_db_table_name('jrMailer', 'url');
    $req = "SELECT * FROM {$tbl} WHERE url_id = '{$uid}'";
    $_rt = jrCore_db_query($req, 'SINGLE');
    if ($_rt && is_array($_rt)) {
        return $_rt;
    }
    return false;
}

/**
 * Record a campaign click
 * @param $user_id int User ID
 * @param $campaign_id int Campaign ID
 * @param $click_url_id int Campaign URL ID
 * @return mixed
 */
function jrMailer_record_campaign_click($user_id, $campaign_id, $click_url_id)
{
    $uid = (int) $user_id;
    $cid = (int) $campaign_id;
    $lid = (int) $click_url_id;
    $tbl = jrCore_db_table_name('jrMailer', 'click');
    $req = "INSERT INTO {$tbl} (click_time, click_url_id, click_campaign_id, click_user_id, click_count)
            VALUES (UNIX_TIMESTAMP(),'{$lid}','{$cid}','{$uid}', 1)
            ON DUPLICATE KEY UPDATE click_count = (click_count + 1)";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Track a campaign view
 * @param $user_id int User ID
 * @param $campaign_id int Campaign ID
 * @return mixed
 */
function jrMailer_track_campaign_view($user_id, $campaign_id)
{
    $uid = (int) $user_id;
    $cid = (int) $campaign_id;
    $uip = jrCore_get_ip();
    $tbl = jrCore_db_table_name('jrMailer', 'track');
    // We reset lat/lng here as our previous record may be from their email provider or anti virus (gmail)
    $req = "INSERT INTO {$tbl} (t_cid,t_uid,t_time,t_ip) VALUES ('{$cid}','{$uid}',UNIX_TIMESTAMP(),'{$uip}')
            ON DUPLICATE KEY UPDATE t_ip = '{$uip}', t_lat = '', t_long = '', t_country = '', t_region = '', t_city = ''";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Set the unsubscribe flag for a user/campaign
 * @param $user_id int User ID
 * @param $campaign_id int Campaign ID
 * @return mixed
 */
function jrMailer_set_unsubscribe_flag($user_id, $campaign_id)
{
    $uid = (int) $user_id;
    $cid = (int) $campaign_id;
    $tbl = jrCore_db_table_name('jrMailer', 'unsubscribe');
    $req = "INSERT IGNORE INTO {$tbl} (u_uid, u_cid, u_time) VALUES ('{$uid}', '{$cid}', UNIX_TIMESTAMP())";
    return jrCore_db_query($req, 'COUNT');
}

/**
 * Create or Update a Campaign and return the campaign ID
 * @param $module string Module campaign is being created for
 * @param $unique_id string Unique identifier for this campaign
 * @param $title string Title of this campaign
 * @param $message string Message of this campaign
 * @return bool|mixed
 */
function jrMailer_get_campaign_id($module, $unique_id, $title, $message)
{
    global $_mods;
    if (isset($_mods[$module])) {
        $uid = jrCore_db_escape($unique_id);
        $ttl = jrCore_db_escape(jrCore_strip_html($title));
        $msg = jrCore_db_escape(jrCore_strip_non_utf8($message));
        $tbl = jrCore_db_table_name('jrMailer', 'campaign');
        $req = "INSERT INTO {$tbl} (c_module, c_unique, c_created, c_updated, c_title, c_message)
                VALUES ('{$module}', '{$uid}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '{$ttl}', '{$msg}')
                ON DUPLICATE KEY UPDATE c_updated = UNIX_TIMESTAMP(), c_id=LAST_INSERT_ID(c_id)";
        $cid = jrCore_db_query($req, 'INSERT_ID');
        if ($cid && $cid > 0) {
            jrMailer_set_active_campaign_id($cid);
            return $cid;
        }
    }
    return false;
}

/**
 * Get info about a specific campaign ID
 * @param $campaign_id int Campaign ID
 * @return mixed
 */
function jrMailer_get_campaign_info_by_id($campaign_id)
{
    $cid = (int) $campaign_id;
    $tbl = jrCore_db_table_name('jrMailer', 'campaign');
    $req = "SELECT * FROM {$tbl} WHERE c_id = '{$cid}'";
    return jrCore_db_query($req, 'SINGLE');
}

/**
 * Get info about a specific campaign ID by module & unique name
 * @param $hash string MD5 hash of campaign_id
 * @return mixed
 */
function jrMailer_get_campaign_info_by_md5($hash)
{
    if (jrCore_checktype($hash, 'md5')) {
        $tbl = jrCore_db_table_name('jrMailer', 'campaign');
        $req = "SELECT * FROM {$tbl} WHERE MD5(c_id) = '{$hash}'";
        return jrCore_db_query($req, 'SINGLE');
    }
    return false;
}

/**
 * Increment a campaign counter by a specified amount
 * @param $campaign_id int Campaign ID
 * @param $field string one of sent|unsub|bounce
 * @param $amount int Amount to increment by
 * @return bool|mixed
 */
function jrMailer_increment_campaign_count($campaign_id, $field, $amount)
{
    $cid = (int) $campaign_id;
    if ($cid && $cid > 0) {
        $amt = (int) $amount;
        $tbl = jrCore_db_table_name('jrMailer', 'campaign');
        $req = "UPDATE {$tbl} SET `c_{$field}` = (`c_{$field}` + {$amt}) WHERE c_id = '{$cid}'";
        return jrCore_db_query($req, 'COUNT');
    }
    return false;
}

/**
 * Create a unique tracking ID for a URL in a campaign email
 * @param $campaign_id int Campaign ID
 * @param $url string URL
 * @return bool|mixed
 */
function jrMailer_create_url_track_id($campaign_id, $url)
{
    $cid = (int) $campaign_id;
    $url = jrCore_db_escape($url);
    $tbl = jrCore_db_table_name('jrMailer', 'url');
    $req = "INSERT INTO {$tbl} (url_cid, url_uri) VALUES ('{$cid}', '{$url}') ON DUPLICATE KEY UPDATE url_id=LAST_INSERT_ID(url_id)";
    $uid = jrCore_db_query($req, 'INSERT_ID');
    if ($uid && $uid > 0) {
        return $uid;
    }
    return false;
}

/**
 * Map URLs in an email to tracking URLs
 * @param $campaign_id int Campaign ID
 * @param $message string Email message
 * @return mixed
 */
function jrMailer_map_campaign_urls($campaign_id, $message)
{
    global $_conf;
    preg_match_all('#href="\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $message, $_urls);
    if ($_urls && is_array($_urls) && isset($_urls[0])) {
        $_urls = array_unique($_urls[0]);
        if ($_urls && is_array($_urls) && count($_urls) > 0) {
            $brl = str_replace('https:', 'http:', $_conf['jrCore_base_url']);
            $mrl = jrCore_get_module_url('jrMailer');
            $_rp = array();
            foreach ($_urls as $url) {
                $url = trim(substr($url, 6));
                if (strpos($url, '/cid=') || strpos($url, '/unsubscribe/') || strpos($url, '/notifications')) {
                    // We've already been mapped
                    continue;
                }
                if (strlen($url) < 256 && strpos(str_replace('https:', 'http:', $url), $brl) === 0) {
                    // This is a LOCAL URL - map
                    $uid                   = jrMailer_create_url_track_id($campaign_id, $url);
                    $_rp['"' . $url . '"'] = "{$_conf['jrCore_base_url']}/{$mrl}/link/{$uid}/%%^%%USER_ID%%^%%";
                }
            }
            if (count($_rp) > 0) {
                return str_replace(array_keys($_rp), $_rp, $message);
            }
        }
    }
    return $message;
}

/**
 * Prepare an email message to be sent in a campaign
 * @param $address string Email Address being set to
 * @param $message string Message being sent
 * @return mixed|string
 */
function jrMailer_prepare_email_message($address, $message)
{
    // Did we map any URLs?
    if (strpos($message, '%%^%%USER_ID%%^%%')) {
        $_id = jrCore_get_flag('jrMailer_tracked_user_ids');
        if (isset($_id[$address])) {
            // make sure User ID is in place
            $message = str_replace('%%^%%USER_ID%%^%%', $_id[$address], $message);
        }
        else {
            // could not find user_id? use original unmapped message
            if ($tmp = jrCore_get_flag('jrMailer_unmapped_message')) {
                $message = $tmp;
            }
        }
    }
    else {
        // We do not know the ID - use original unmapped message
        if ($tmp = jrCore_get_flag('jrMailer_unmapped_message')) {
            $message = $tmp;
        }
    }

    // Do we have a tracker for this address?
    $_tr = jrCore_get_flag('jrMailer_tracked_addresses');
    if ($_tr && is_array($_tr) && isset($_tr[$address])) {
        // We have a tracker for this email - insert into body
        if (strpos($message, '</body>')) {
            $message = str_replace('</body>', '<img src="' . $_tr[$address] . '" width="1" height="1" alt=""></body>', $message);
        }
        else {
            $message = $message . "\n" . '<img src="' . $_tr[$address] . '" width="1" height="1" alt="">';
        }
    }
    return $message;
}

//-----------------------------------
// PLUGINS
//-----------------------------------

/**
 * @ignore
 * Process Bounces plugin
 * @return bool
 */
function _jrMailer_smtp_process_bounces()
{
    return true;
}

/**
 * @ignore
 * @param $_email_to mixed Email Addresses to send email to (single or array)
 * @param $_user array User info array
 * @param $_conf array Global Config
 * @param $_email_info array Extra email arguments
 * @return int
 */
function _jrMailer_smtp_send_email($_email_to, $_user, $_conf, $_email_info)
{
    // Bring in Swift Mailer
    require_once APP_DIR . '/modules/jrMailer/contrib/swiftmailer/swift_required.php';

    // $_email_to is an array containing all of the email addresses this message
    // is being sent to.
    //
    // $_email_info is an array of information about the email being sent, including:
    // required - 'subject'
    // required - 'message'
    // required - 'from'
    // optional - 'from_name'
    // optional - 'priority'  (int 1 -> 5 = highest,high,normal,low,lowest)
    // optional - 'send_as_html' = true; Send as an HTML email
    // optional - 'campaign_id' = campaign analytics ID (must be sent as HTML)
    // optional - 'mailing_module' = module sending the email
    // optional - 'mailing_event' = specific jrUser_notify event from mailing module
    // optional - 'headers' = array of additional email text headers
    // optional = 'queue_sleep' = seconds to sleep before delivering email
    //
    // Our module config also includes some items:
    // 'from' - specifies email address for bounces
    // 'return_email' - specifies email address for bounces

    // Init transport
    // See what type of transport we are using:
    // SMTP or Mail
    if (!isset($GLOBALS['swift_mailer_object'])) {
        switch (strtolower($_conf['jrMailer_transport'])) {
            case 'smtp':
                if (function_exists('proc_open')) {
                    if (isset($_conf['jrMailer_smtp_encryption']) && $_conf['jrMailer_smtp_encryption'] != 'none') {
                        $trs = Swift_SmtpTransport::newInstance($_conf['jrMailer_smtp_host'], intval($_conf['jrMailer_smtp_port']))
                            ->setUsername($_conf['jrMailer_smtp_user'])
                            ->setPassword($_conf['jrMailer_smtp_pass'])
                            ->setEncryption($_conf['jrMailer_smtp_encryption'])
                            ->setTimeout(10);
                    }
                    else {
                        $trs = Swift_SmtpTransport::newInstance($_conf['jrMailer_smtp_host'], intval($_conf['jrMailer_smtp_port']))
                            ->setUsername($_conf['jrMailer_smtp_user'])
                            ->setPassword($_conf['jrMailer_smtp_pass'])
                            ->setTimeout(10);
                    }
                }
                else {
                    if (!isset($GLOBALS['jrMailer_smtp_send_email_error'])) {
                        jrCore_logger('CRI', 'SMTP transport enabled but PHP proc_open function is disabled!');
                        $GLOBALS['jrMailer_smtp_send_email_error'] = 1;
                    }
                    $trs = Swift_MailTransport::newInstance();
                }
                break;
            default:
                $trs = Swift_MailTransport::newInstance();
                break;
        }

        // Create the message using the transport
        $GLOBALS['swift_mailer_object'] = Swift_Mailer::newInstance($trs);
    }

    // Create a message
    $msg = Swift_Message::newInstance($_email_info['subject']);

    if (isset($_email_info['headers']) && is_array($_email_info['headers'])) {
        $headers = $msg->getHeaders();
        foreach ($_email_info['headers'] as $type => $text) {
            $headers->addTextHeader($type, $text);
        }
    }

    // Set From
    if (!isset($_email_info['from']) || !jrCore_checktype($_email_info['from'], 'email')) {
        if (isset($_conf['jrMailer_from_email']) && strpos($_conf['jrMailer_from_email'], '@')) {
            $_email_info['from'] = $_conf['jrMailer_from_email'];
        }
        elseif (isset($_SERVER['SERVER_ADMIN']) && jrCore_checktype($_SERVER['SERVER_ADMIN'], 'email')) {
            $_email_info['from'] = $_SERVER['SERVER_ADMIN'];
        }
        else {
            return 0;
        }
    }

    // Do we have a friendly name for our FROM email?
    if (!isset($_email_info['from_name']{0})) {
        if (isset($_conf['jrMailer_from_name']) && strlen($_conf['jrMailer_from_name']) > 0 && $_email_info['from'] == $_conf['jrMailer_from_email']) {
            $msg->setFrom(array($_email_info['from'] => $_conf['jrMailer_from_name']));
        }
        else {
            $msg->setFrom(array($_email_info['from']));
        }
    }
    else {
        $msg->setFrom(array($_email_info['from'] => $_email_info['from_name']));
    }

    // Check for return email
    if (isset($_conf['jrMailer_return_email']{0}) && strpos($_conf['jrMailer_return_email'], '@')) {
        $msg->setReturnPath($_conf['jrMailer_return_email']);
    }

    // Priority
    if (isset($_email_info['priority']) && jrCore_checktype($_email_info['priority'], 'number_nz')) {
        $msg->setPriority($_email_info['priority']);
    }

    // Attachment
    if (isset($_email_info['attachments']) && is_array($_email_info['attachments'])) {
        // We have a default 10mb limit here for file attachments
        $max_size = 10000000;
        if (isset($_email_info['max_attachment_size']) && jrCore_checktype($_email_info['max_attachment_size'], 'number_nz')) {
            // Overridden
            $max_size = $_email_info['max_attachment_size'];
        }
        foreach ($_email_info['attachments'] as $file) {
            if (is_file($file) && filesize($file) < $max_size) {
                $msg->attach(Swift_Attachment::fromPath($file));
            }
        }
    }

    // See if we have HTML in the body of the message - if we do, send as HTML OR
    if ((isset($_conf['jrMailer_send_as_html']) && $_conf['jrMailer_send_as_html'] == 'on') || strpos(' ' . trim($_email_info['message']), '<body') === 0) {
        $_email_info['send_as_html'] = true;
    }

    // Add Body of message
    if (isset($_email_info['send_as_html']) && $_email_info['send_as_html'] !== false) {
        // Convert newlines to breaks
        if (strpos("\n", $_email_info['message'])) {
            $_email_info['message'] = nl2br($_email_info['message']);
        }
        $msg->setBody($_email_info['message'], 'text/html');
        $msg->addPart(jrCore_strip_html($_email_info['message']), 'text/plain');
    }
    else {
        $msg->setBody($_email_info['message']);
    }

    // Send the message
    $bad = array();
    $num = 0;
    foreach ($_email_to as $address) {

        $msg->setTo($address);

        if (isset($_email_info['send_as_html']) && $_email_info['send_as_html'] !== false) {
            $body = jrMailer_prepare_email_message($address, $_email_info['message']);
            $msg->setBody($body, 'text/html');
        }

        try {
            $num += $GLOBALS['swift_mailer_object']->send($msg, $bad);
        }
        catch (Exception $e) {
            $_rp = array(
                'mailinfo' => $_email_info,
                'errormsg' => $e->getMessage()
            );
            jrCore_logger('CRI', 'Error sending email using configured transport', $_rp);
        }
    }
    return $num;
}
