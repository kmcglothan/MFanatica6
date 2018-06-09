<?php
/**
 * Jamroom Event Calendar module
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

//------------------------------
// create
//------------------------------
function view_jrEvent_create($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrEvent');
    jrProfile_check_disk_usage();

    // Start our create form
    $tmp = jrEvent_page_banner_item_jumper();
    jrCore_page_banner(1, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 1,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    // Event Title
    $_tmp = array(
        'name'     => 'event_title',
        'label'    => 2,
        'help'     => 3,
        'placeholder' => 148,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Event Location
    $_tmp = array(
        'name'     => 'event_location',
        'label'    => 6,
        'help'     => 7,
        'type'     => 'text',
        'placeholder' => 149,
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Event Date
    $_tmp = array(
        'name'     => 'event_date',
        'label'    => 11,
        'help'     => 12,
        'type'     => 'datetime',
        'validate' => 'date',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Event Description
    $_tmp = array(
        'name'     => 'event_description',
        'label'    => 4,
        'help'     => 5,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'placeholder' => 150,
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Event Image
    $_tmp = array(
        'name'     => 'event_image',
        'label'    => 8,
        'help'     => 9,
        'text'     => 10,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrEvent_create_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrEvent');
    jrCore_form_validate($_post);

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrEvent', 'create', $_post);

    // Check for recurring parameters
    if (isset($_rt['event_end_date']) && isset($_rt['event_recurring']) && $_rt['event_date'] > $_rt['event_end_date'] && $_rt['event_recurring'] != 'no') {
        jrCore_set_form_notice('error', $_lang['jrEvent'][19]);
        jrCore_form_result();
    }

    // Get array of all event dates
    $_event_dates = jrEvent_get_event_dates($_rt);

    // Check that this will not exceed the maximum events allowed for the quota
    $e_cnt = count($_event_dates);
    $q_max = isset($_user["quota_jrEvent_max_items"]) ? (int) $_user["quota_jrEvent_max_items"] : 0;
    $p_cnt = isset($_user["profile_jrEvent_item_count"]) ? (int) $_user["profile_jrEvent_item_count"] : 0;
    if ($q_max > 0 && $p_cnt + $e_cnt > $q_max) {
        if ($e_cnt == 1) {
            jrCore_set_form_notice('error', $_lang['jrEvent'][28]);
        }
        else {
            jrCore_set_form_notice('error', $_lang['jrEvent'][29]);
        }
        jrCore_form_delete_session();
        jrProfile_reset_cache();
        jrCore_form_result();
    }

    // Add in our SEO URL name
    $_rt['event_title_url'] = jrCore_url_string($_rt['event_title']);

    // Create the event(s)
    if (isset($_rt['event_recurring'])) {
        unset($_rt['event_recurring']);
    }
    if (isset($_rt['event_end_date'])) {
        unset($_rt['event_end_date']);
    }

    // If we are entering multiple events AND we have been given and END TIME for the events
    // (i.e. 4:00PM -> 6:00PM) then we need to compute the length of the event and make
    // sure each event is setup for the same length
    $add = 0;
    if ($e_cnt > 1 && isset($_rt['event_end_day'])) {
        $add = ($_rt['event_end_day'] - $_rt['event_date']);
    }

    $eid = false;
    $sav = 0;
    foreach ($_event_dates as $event_date) {

        $_rt['event_date'] = $event_date;
        if ($e_cnt > 1 && $sav > 0) {
            // This is a recurring event - we want to be able to "group"
            // these events together so if we decide to delete ONE future
            // event, we can be prompted to remove ALL future events
            $_rt['event_linked_id'] = $sav;
            if ($add > 0) {
                $_rt['event_end_day'] = ($event_date + $add);
            }
        }

        $eid = jrCore_db_create_item('jrEvent', $_rt);
        if (!$eid) {
            jrCore_set_form_notice('error', $_lang['jrEvent'][20]);
            jrCore_form_result();
        }

        // Save any uploaded media files
        jrCore_save_all_media_files('jrEvent', 'create', $_user['user_active_profile_id'], $eid);

        // Save and add our FIRST Event to our actions...
        if ($sav == 0) {
            // Add to Actions...
            jrCore_run_module_function('jrAction_save', 'create', 'jrEvent', $eid);
            $sav = $eid;
        }
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache();

    if (count($_event_dates) > 1) {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
    }
    if ($eid) {
        jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$eid}");
    }
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrEvent_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrEvent');

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', $_lang['jrEvent'][25]);
    }
    $_rt = jrCore_db_get_item('jrEvent', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', $_lang['jrEvent'][26]);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $tmp = jrEvent_page_banner_item_jumper();
    jrCore_page_banner(27, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 27,
        'cancel'       => jrCore_is_profile_referrer(),
        'values'       => $_rt
    );
    jrCore_form_create($_tmp);

    // Item ID
    $_tmp = array(
        'name'     => 'id',
        'type'     => 'hidden',
        'value'    => $_post['id'],
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Event Title
    $_tmp = array(
        'name'     => 'event_title',
        'label'    => 2,
        'help'     => 3,
        'type'     => 'text',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Event Location
    $_tmp = array(
        'name'     => 'event_location',
        'label'    => 6,
        'help'     => 7,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Event Date
    $_tmp = array(
        'name'     => 'event_date',
        'label'    => 11,
        'help'     => 12,
        'type'     => 'datetime',
        'validate' => 'date',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Event Description
    $_tmp = array(
        'name'     => 'event_description',
        'label'    => 4,
        'help'     => 5,
        'type'     => 'editor',
        'validate' => 'allowed_html',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Event Image
    $_tmp = array(
        'name'     => 'event_image',
        'label'    => 8,
        'help'     => 9,
        'text'     => 10,
        'value'    => $_rt,
        'type'     => 'image',
        'required' => false
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrEvent_update_save($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrEvent');
    jrCore_form_validate($_post);

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', $_lang['jrEvent'][25]);
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrEvent', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', $_lang['jrEvent'][30]);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrEvent', 'update', $_post);

    // Add in our SEO URL name and other custom stuff
    $_sv['event_title_url'] = jrCore_url_string($_sv['event_title']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrEvent', $_post['id'], $_sv);

    // Save any uploaded media file
    jrCore_save_all_media_files('jrEvent', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add action
    jrCore_run_module_function('jrAction_save', 'update', 'jrEvent', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['event_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrEvent_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', $_lang['jrEvent'][25]);
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrEvent', $_post['id']);

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item
    jrCore_db_delete_item('jrEvent', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// attend
//------------------------------
function view_jrEvent_attend($_post, $_user, $_conf)
{
    // [_uri] => /event/attend/attending/178/1/__ajax=1
    // [module_url] => event
    // [module] => jrEvent
    // [option] => attend
    // [_1] => 178 (the event id)
    // [__ajax] => 1

    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Check that we get good IDs
    if (!isset($_post['_1']) || !jrCore_checktype($_post['_1'], 'number_nz')) {
        $_rs = array('error' => 'invalid event_id - please try again');
        jrCore_json_response($_rs);
    }

    // Toggle the user's attending status
    $tbl   = jrCore_db_table_name('jrEvent', 'attendee');
    $req   = "DELETE FROM {$tbl} WHERE `attendee_event_id` = '{$_post['_1']}' AND `attendee_user_id` = '{$_user['_user_id']}'";
    $state = 'not_attending';
    if (!jrCore_checktype(jrCore_db_query($req, 'COUNT'), 'number_nz')) {
        $req = "INSERT IGNORE INTO {$tbl} (attendee_created, attendee_user_id, attendee_event_id, attendee_notified, attendee_active) VALUES (UNIX_TIMESTAMP(), '{$_user['_user_id']}', '{$_post['_1']}', 0, 1)";
        jrCore_db_query($req);
        $state = 'attending';

        // Add to Actions...
        $_s = array(
            "search"       => array(
                "action_mode = attend",
                "action_module = jrEvent",
                "action_item_id = {$_post['_1']}",
                "_user_id = {$_user['_user_id']}"
            ),
            "return_count" => true,
        );
        if (!jrCore_checktype(jrCore_db_search_items('jrAction', $_s), 'number_nz')) {
            $_as = array(
                'quota_jrAction_allowed'  => $_user['quota_jrAction_allowed'],
                'action_original_module'  => $_post['module'],
                'action_original_item_id' => (int) $_post['_1'],
                'ignore_ds_item'          => true,
                '_profile_id'             => jrUser_get_profile_home_key('_profile_id'),
                '_user_id'                => $_user['_user_id']
            );
            jrCore_run_module_function('jrAction_save', 'attend', 'jrEvent', $_post['_1'], $_as);
        }
    }

    // for the jrTrace module
    $_data = array(
        '_item_id'          => $_post['_1'],
        'attendee_event_id' => $_post['_1'],
        'attending_state'   => $state,
        'attendee_user_id'  => $_user['_user_id'],
    );
    jrCore_trigger_event('jrEvent', 'attending', $_data);

    $_rs = array('OK' => 1);
    jrCore_json_response($_rs);
}

//------------------------------
// calendar
//------------------------------
function view_jrEvent_calendar($_post, $_user, $_conf)
{
    // site.com/event/calendar/06/2014
    $wanted_m = (isset($_post['_1'])) ? $_post['_1'] : date('n');
    $wanted_y = (isset($_post['_2'])) ? $_post['_2'] : date('Y');

    $_rep = array(
        'month'  => $wanted_m,
        'year'   => $wanted_y,
        '_years' => jrEvent_get_year_range(),
    );

    return jrCore_parse_template('site_calendar.tpl', $_rep, 'jrEvent');
}

//------------------------------
// default
//------------------------------
function view_jrEvent_default($_post, $_user, $_conf)
{
    $_rep = array();
    if (isset($_post['day']) && isset($_post['month']) && isset($_post['year'])) {
        // day
        $_rep['ts_start'] = mktime(0, 0, 0, $_post['month'], $_post['day'], $_post['year']);
        $_rep['ts_end']   = mktime(0, 0, 0, $_post['month'], $_post['day'] + 1, $_post['year']);
    }
    elseif (isset($_post['month']) && isset($_post['year'])) {
        // month
        $_rep['ts_start'] = mktime(0, 0, 0, $_post['month'], 1, $_post['year']);
        $_rep['ts_end']   = mktime(0, 0, 0, $_post['month'] + 1, 1, $_post['year']);
    }
    elseif (isset($_post['year'])) {
        // month
        $_rep['ts_start'] = mktime(0, 0, 0, 1, 1, $_post['year']);
        $_rep['ts_end']   = mktime(0, 0, 0, 1, 1, $_post['year'] + 1);
    }
    return jrCore_parse_template('index.tpl', $_rep, 'jrEvent');
}

//------------------------------
// attending
//------------------------------
function view_jrEvent_attending($_post, $_user, $_conf)
{
    // Must be logged
    jrUser_session_require_login();
    jrCore_validate_location_url();
    $_ln = jrUser_load_lang_strings();

    // Banner
    $tmp = jrCore_page_button('all', 'all', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/attending/all')");
    jrCore_page_banner($_ln['jrEvent'][144], $tmp);

    // Get all items liked
    $tbl = jrCore_db_table_name('jrEvent', 'attendee');
    $req = "SELECT attendee_event_id FROM {$tbl} WHERE attendee_user_id = '{$_user['_user_id']}' AND attendee_active = 1 ORDER BY attendee_created DESC";
    $_rt = jrCore_db_query($req, 'attendee_event_id');
    if ($_rt && is_array($_rt) && count($_rt) > 0) {
        // Get events
        $_s = array(
            "search"        => array('_item_id IN ' . implode(',', array_keys($_rt))),
            "order_by"      => array('event_date' => 'numerical_asc'),
            "quota_check"   => false,
            "privacy_check" => false,
            "limit"         => count($_rt)
        );
        if (!isset($_post['_1']) || $_post['_1'] != 'all') {
            $_s['search'][] = 'event_date >= ' . time();
        }
        $_et = jrCore_db_search_items('jrEvent', $_s);
        if ($_et && is_array($_et['_items']) && count($_et['_items']) > 0) {
            $html = jrCore_parse_template('item_list.tpl', $_et, 'jrEvent');
            $html .= jrCore_parse_template('list_pager.tpl', $_et, 'jrCore');
            jrCore_page_custom($html);
        }
        else {
            jrCore_page_note($_ln['jrEvent'][145]);
        }
    }
    else {
        jrCore_page_note($_ln['jrEvent'][146]);
    }
    jrCore_page_display();
}
