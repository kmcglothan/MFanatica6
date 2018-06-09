<?php
/**
 * Jamroom SoundCloud module
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

//------------------------------
// create
//------------------------------
function view_jrSoundCloud_create($_post, $_user, $_conf)
{
    // Must be logged in to create a new audio file
    jrUser_session_require_login();

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    if (strlen($_conf['jrSoundCloud_client_id']) == 0 || strlen($_conf['jrSoundCloud_client_secret']) == 0) {
        // module is turned on, but no client id is set, all creates will fail.
        jrCore_logger('CRI', 'jrSoundCloud: A user tried to add a soundcloud item, but the SoundCloud ID and Secret have not been set, so could not connect. Setup the SoundCloud module, or deactivate it. ');
        jrCore_notice_page('notice', 'SoundCloud id and secret have not been set, can not connect. Contact admin.');
    }

    // Start our create form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrSoundCloud', 'soundcloud_title', $_sr, 'create', 'update');
    jrCore_page_banner(12, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 2,
        'cancel'       => jrCore_is_profile_referrer()
    );
    jrCore_form_create($_tmp);

    jrCore_page_note('<div class="p5">' . $_lang['jrSoundCloud'][58] . '&nbsp' . jrCore_page_button('as', $_lang['jrSoundCloud'][32], "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/search')") . '</div>');

    // SoundCloud ID
    $_tmp = array(
        'name'       => 'soundcloud_id',
        'label'      => 3,
        'help'       => 4,
        'type'       => 'text',
        'validate'   => 'allowed_html',
        'required'   => true,
        'onkeypress' => "if (event && event.keyCode == 13) return false;"
    );
    jrCore_form_field_create($_tmp);

    // Tags option
    if (jrCore_module_is_active('jrTags') && jrUser_check_quota_access('jrTags')) {
        $_tmp = array(
            'name'          => 'soundcloud_tags',
            'label'         => 65,
            'help'          => 66,
            'default'       => 'off',
            'type'          => 'checkbox',
            'validate'      => 'onoff',
            'required'      => true,
            'form_designer' => false
        );
        jrCore_form_field_create($_tmp);
    }
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrSoundCloud_create_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrSoundCloud');

    $soundcloud_id = jrSoundCloud_extract_id($_post['soundcloud_id']);
    if (!jrCore_checktype($soundcloud_id, 'number_nz')) {
        jrCore_set_form_notice('error', 5);
        jrCore_form_result();
    }

    // See if user has already uploaded this ID
    $_sc = array(
        "search"              => array(
            "_profile_id = {$_user['user_active_profile_id']}",
            "soundcloud_id = {$soundcloud_id}"
        ),
        'skip_triggers'       => true,
        'return_item_id_only' => true,
        'limit'               => 1
    );
    $_sc = jrCore_db_search_items('jrSoundCloud', $_sc);
    if ($_sc && $_sc > 0) {
        jrCore_set_form_notice('error', 61);
        jrCore_form_result();
    }
    $_data = jrSoundCloud_get_data($soundcloud_id);
    if (!$_data || !is_array($_data)) {
        jrCore_set_form_notice('error', 6);
        jrCore_form_result();
    }

    // Add in our SoundCloud data
    $_rt                           = array();
    $_rt['soundcloud_id']          = $soundcloud_id;
    $_rt['soundcloud_title']       = $_data['title'];
    $_rt['soundcloud_genre']       = $_data['genre'];
    $_rt['soundcloud_genre_url']   = jrCore_url_string($_data['genre']);
    $_rt['soundcloud_artist']      = $_data['user']['username'];
    $_rt['soundcloud_duration']    = jrSoundCloud_duration_to_readable($_data['duration']);
    $_rt['soundcloud_description'] = $_data['description'];
    $_rt['soundcloud_title_url']   = jrCore_url_string($_rt['soundcloud_title']);
    if (isset($_data['artwork_url']) && jrCore_checktype($_data['artwork_url'], 'url')) {
        $_rt['soundcloud_artwork_url'] = $_data['artwork_url'];
    }
    elseif (isset($_data['user']['avatar_url']) && jrCore_checktype($_data['user']['avatar_url'], 'url')) {
        $_rt['soundcloud_artwork_url'] = $_data['user']['avatar_url'];
    }
    else {
        $_rt['soundcloud_artwork_url'] = '';
    }
    // Add in any custom fields
    $_sv = jrCore_form_get_save_data('jrSoundCloud', 'create', $_post);
    if (isset($_sv['soundcloud_id'])) {
        unset($_sv['soundcloud_id']);
    }
    if (isset($_sv['soundcloud_tags'])) {
        unset($_sv['soundcloud_tags']);
    }
    $_rt = array_merge($_rt, $_sv);
    // All good - Create item
    $scid = jrCore_db_create_item('jrSoundCloud', $_rt);
    if (!$scid) {
        jrCore_set_form_notice('error', 7);
        jrCore_form_result();
    }
    // Save any uploaded media files
    jrCore_save_all_media_files('jrSoundCloud', 'create', $_user['user_active_profile_id'], $scid);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'create', 'jrSoundCloud', $scid);

    // See if we are creating tags
    if (isset($_data['tag_list']) && strlen($_data['tag_list']) > 0 && isset($_post['soundcloud_tags']) && $_post['soundcloud_tags'] == 'on') {

        // Tags can come in as single words or "quoted strings"
        // [tag_list] => miguel girls simplethings "girls soundtrack" "girls: volumer 2: all adventurous women do" "miguel music"
        $_tags = explode(' ', trim($_data['tag_list']));
        if (isset($_tags) && is_array($_tags)) {
            $beg = '';
            $add = true;
            foreach ($_tags as $tag) {
                $tag = trim($tag);
                if ($add && strpos(' ' . $tag, '"')) {
                    // We have opening word of multi-word tag
                    $beg = str_replace('"', '', $tag);
                    $add = false;
                }
                elseif (!$add && strlen($beg) > 0) {
                    if (strpos($tag, '"')) {
                        // End of tag
                        $beg .= ' ' . str_replace('"', '', $tag);
                        $tag = $beg;
                        $beg = '';
                        $add = true;
                    }
                    else {
                        $beg .= ' ' . $tag;
                    }
                }
                else {
                    $add = true;
                }
                if ($add) {
                    $_tmp = array(
                        'tag_text'         => $tag,
                        'tag_url'          => jrCore_url_string($tag),
                        'tag_module'       => 'jrSoundCloud',
                        'tag_item_id'      => $scid,
                        'tag_profile_id'   => $_user['_profile_id'],
                        'tag_item_created' => 'UNIX_TIMESTAMP()'
                    );
                    jrCore_db_create_item('jrTags', $_tmp);
                }
            }
        }
    }

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$scid}/{$_rt['soundcloud_title_url']}");
}

//------------------------------
// update
//------------------------------
function view_jrSoundCloud_update($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrUser_check_quota_access('jrSoundCloud');

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 9);
    }
    $_rt = jrCore_db_get_item('jrSoundCloud', $_post['id']);
    if (!$_rt) {
        jrCore_notice_page('error', 10);
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Start output
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrSoundCloud', 'soundcloud_title', $_sr, 'create', 'update');
    jrCore_page_banner(11, $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 13,
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

    // SoundCloud Title
    $_tmp = array(
        'name'     => 'soundcloud_title',
        'label'    => 14,
        'help'     => 15,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // SoundCloud Genre
    $_tmp = array(
        'name'     => 'soundcloud_genre',
        'label'    => 16,
        'help'     => 17,
        'type'     => 'select_and_text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // SoundCloud Artist Name
    $_tmp = array(
        'name'     => 'soundcloud_artist',
        'label'    => 18,
        'help'     => 19,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // SoundCloud Description
    $_tmp = array(
        'name'     => 'soundcloud_description',
        'label'    => 20,
        'help'     => 21,
        'type'     => 'textarea',
        'validate' => 'printable',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrSoundCloud_update_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrSoundCloud');

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 22);
        jrCore_form_result();
    }

    // Get data
    $_rt = jrCore_db_get_item('jrSoundCloud', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 23);
        jrCore_form_result();
    }

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrSoundCloud', 'update', $_post);

    // Add in our SEO URL names
    $_sv['soundcloud_title_url'] = jrCore_url_string($_sv['soundcloud_title']);
    $_sv['soundcloud_genre_url'] = jrCore_url_string($_sv['soundcloud_genre']);

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrSoundCloud', $_post['id'], $_sv);

    // Save any uploaded media files
    jrCore_save_all_media_files('jrSoundCloud', 'update', $_user['user_active_profile_id'], $_post['id']);

    // Add to Actions...
    jrCore_run_module_function('jrAction_save', 'update', 'jrSoundCloud', $_post['id']);

    jrCore_form_delete_session();
    jrProfile_reset_cache();
    //jrCore_set_form_notice('success',24);
    //jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$_post['id']}");
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_user['profile_url']}/{$_post['module_url']}/{$_post['id']}/{$_sv['soundcloud_title_url']}");
}

//------------------------------
// delete
//------------------------------
function view_jrSoundCloud_delete($_post, $_user, $_conf)
{
    // Must be logged in
    jrUser_session_require_login();
    jrCore_validate_location_url();

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 9);
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrSoundCloud', $_post['id']);

    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }
    // Delete item and any associated files
    jrCore_db_delete_item('jrSoundCloud', $_post['id']);
    jrProfile_reset_cache();
    jrCore_form_result('delete_referrer');
}

//------------------------------
// search
// If set, $_post['_1'] is the search string and $_post['_2'] is the page number
//------------------------------
function view_jrSoundCloud_search($_post, $_user, $_conf)
{
    // Must be logged in to search soundcloud
    jrUser_session_require_login();

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // Start our search form
    $_sr = array(
        "_profile_id = {$_user['user_active_profile_id']}",
    );
    $tmp = jrCore_page_banner_item_jumper('jrSoundCloud', 'soundcloud_title', $_sr, 'create', 'update');
    jrCore_page_banner(30, $tmp);
    $ss    = (isset($_post['_1']) && $_post['_1'] != '') ? urldecode($_post['_1']) : $_user['profile_name'];
    $page  = (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? $_post['p'] : 1;
    $limit = (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 10;

    // Form init
    $cancel = $_conf['jrCore_base_url'] . '/' . $_user['profile_url'] . '/' . $_post['module_url'];
    $_tmp   = array(
        'submit_value' => 31,
        'cancel'       => $cancel
    );
    jrCore_form_create($_tmp);

    // Current Search
    $_tmp = array(
        'name'     => 'current_search',
        'type'     => 'hidden',
        'value'    => $ss,
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Page
    $_tmp = array(
        'name'     => 'page',
        'type'     => 'hidden',
        'value'    => $page,
        'validate' => 'number_nz'
    );
    jrCore_form_field_create($_tmp);

    // Search String
    $_tmp = array(
        'name'     => 'search',
        'value'    => $ss,
        'label'    => 67,
        'help'     => 33,
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $areas = array(
        'q'      => 'everywhere',
        'tags'   => 'tags',
        'genres' => 'genres',
    );
    // Search Area
    $_tmp = array(
        'name'     => 'search_area',
        'value'    => (isset($_post['_2'])) ? $_post['_2'] : 'q',
        'options'  => $areas,
        'label'    => 40,
        'help'     => 46,
        'type'     => 'select',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Order by
    $_ob  = array(
        ''           => '-',
        'created_at' => 'newest',
        'hotness'    => 'hotness'
    );
    $_tmp = array(
        'name'     => 'search_order',
        'value'    => (isset($_post['_4'])) ? $_post['_4'] : '',
        'options'  => $_ob,
        'label'    => 49,
        'help'     => 50,
        'type'     => 'select',
        'validate' => 'not_empty',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Get some search results
    $url    = "http://api.soundcloud.com/tracks.json?client_id={$_conf['jrSoundCloud_client_id']}";
    $ssc    = urlencode($ss);
    $offset = ($page - 1) * $limit;
    $area   = (isset($_post['_2'])) ? strip_tags(html_entity_decode($_post['_2'], ENT_QUOTES)) : 'q';

    if (isset($_post['_4'])) {
        switch ($_post['_4']) {
            case 'newest':
                $order       = '&order=created_at';
                $_post['_4'] = 'created_at';
                break;
            case 'hotness':
                $order       = '&order=hotness';
                $_post['_4'] = 'hotness';
                break;
            default:
                $order       = '';
                $_post['_4'] = '';
                break;
        }
    }
    else {
        $order       = '';
        $_post['_4'] = '';
    }

    $url .= "&{$area}={$ssc}&offset={$offset}&limit={$limit}$order";
    $res = jrCore_load_url($url);

    // If CURL doesn't work, try file_get_contents
    if (!isset($res) || strlen($res) < 100) {
        $res = file_get_contents($url);
        if (!isset($res) || strlen($res) < 100) {
            jrCore_logger('CRI', "jrSoundCloud failed to get any response from soundcloud: {$url}");
        }
    }
    if (isset($res) && !jrCore_checktype($res, 'json')) {
        // not a json response from soundcloud
        $_debug = array(
            'what' => 'soundcloud send a response but it was not json',
            '$res' => $res,
            '$url' => $url
        );
        jrCore_logger('CRI', 'error trying to retrieve soundcloud info', $_debug);
    }
    $_sc = json_decode($res, true);

    // Get soundcloud tracks already imported
    $_sp  = array(
        'search'         => array(
            "_profile_id = {$_user['user_active_profile_id']}"
        ),
        'return_keys'    => array('soundcloud_id'),
        'privacy_check'  => false,
        'ignore_pending' => true,
        'limit'          => 1000
    );
    $_rt  = jrCore_db_search_items('jrSoundCloud', $_sp);
    $_ids = array();
    if (isset($_rt) && isset($_rt['_items'])) {
        foreach ($_rt['_items'] as $_itm) {
            $_ids["{$_itm['soundcloud_id']}"] = 1;
        }
    }

    if (isset($_sc) && is_array($_sc)) {

        $dat[0]['title'] = $_lang['jrSoundCloud'][34];
        $dat[1]['title'] = $_lang['jrSoundCloud'][25];
        $dat[2]['title'] = $_lang['jrSoundCloud'][26];
        $dat[3]['title'] = $_lang['jrSoundCloud'][27];
        $dat[4]['title'] = $_lang['jrSoundCloud'][51];
        $dat[5]['title'] = $_lang['jrSoundCloud'][52];
        $dat[6]['title'] = $_lang['jrSoundCloud'][28];
        $dat[7]['title'] = $_lang['jrSoundCloud'][63];
        if (jrCore_module_is_active('jrTags') && jrUser_check_quota_access('jrTags')) {
            $dat[8]['title'] = $_lang['jrSoundCloud'][64];
        }
        else {
            $dat[8]['title'] = "-";
        }
        jrCore_page_table_header($dat);

        $murl = jrCore_get_module_url('jrImage');
        foreach ($_sc as $_x) {

            // Show the search results
            $purl            = "{$_conf['jrCore_base_url']}/{$murl}/img/{$_post['module_url']}/button_play_soundcloud.png";
            $dat[0]['title'] = '<a onclick="window.open(\'http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F' . $_x['id'] . '&auto_play=TRUE&show_artwork=true\',\'Popup\',\'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=520,height=186,left=200,top=200\');"><img src="' . $purl . '" alt="play" border="0"></a>';
            $dat[0]['class'] = 'center';
            $dat[1]['title'] = $_x['title'];
            $dat[2]['title'] = $_x['user']['username'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = $_x['genre'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = $_x['tag_list'];
            $dat[5]['title'] = substr($_x['created_at'], 0, 10);
            $dat[5]['class'] = 'center';
            $dat[6]['title'] = jrSoundCloud_duration_to_readable($_x['duration']);
            $dat[6]['class'] = 'center';
            if (isset($_ids[$_x['id']])) {
                $dat[7]['title'] = '';
                $dat[8]['title'] = '';
            }
            else {
                $dat[7]['title'] = '<center><input type="checkbox" class="sc_import" name="import_sc_' . $_x['id'] . '"></center>';
                if (jrCore_module_is_active('jrTags') && jrUser_check_quota_access('jrTags') && isset($_x['tag_list']) && $_x['tag_list'] != '') {
                    $dat[8]['title'] = '<center><input type="checkbox" class="sc_import" name="import_tags_' . $_x['id'] . '"></center>';
                }
                else {
                    $dat[8]['title'] = '';
                }
            }
            jrCore_page_table_row($dat);
        }

        $dat             = array();
        $dat[0]['title'] = '&nbsp;';
        $dat[0]['class'] = '" colspan="7';
        $dat[1]['title'] = '<input type="checkbox" class="form_checkbox" onclick="$(\'.sc_import\').prop(\'checked\',$(this).prop(\'checked\'));">';
        $dat[1]['class'] = 'center';
        $dat[2]['title'] = $_lang['jrSoundCloud'][45];
        jrCore_page_table_row($dat);
        jrCore_page_table_footer();
    }
    else {
        jrCore_page_note($_lang['jrSoundCloud'][41]);
    }

    $_pg = array('info' => array(
        'total_pages' => 100,
        'this_page'   => $page,
        'next_page'   => $page + 1,
        'prev_page'   => $page - 1
    ));
    jrCore_page_table_pager($_pg);
    jrCore_page_display();
}

//------------------------------
// search_save
//------------------------------
function view_jrSoundCloud_search_save($_post, &$_user, &$_conf)
{
    // Must be logged in
    jrUser_session_require_login();

    // Validate all incoming posted data
    jrCore_form_validate($_post);
    jrUser_check_quota_access('jrSoundCloud');

    // Get language strings
    $_lang = jrUser_load_lang_strings();

    // Do any imports
    $ctr = 0;
    foreach ($_post as $k => $v) {
        if (substr($k, 0, 10) == 'import_sc_' && $v == 'on' && jrCore_checktype(substr($k, 10), 'number_nz')) {
            $soundcloud_id                 = substr($k, 10);
            $_data                         = jrSoundCloud_get_data($soundcloud_id);
            $_sv                           = array();
            $_sv['soundcloud_id']          = $soundcloud_id;
            $_sv['soundcloud_title']       = $_data['title'];
            $_sv['soundcloud_genre']       = $_data['genre'];
            $_sv['soundcloud_genre_url']   = jrCore_url_string($_data['genre']);
            $_sv['soundcloud_artist']      = $_data['user']['username'];
            $_sv['soundcloud_duration']    = jrSoundCloud_duration_to_readable($_data['duration']);
            $_sv['soundcloud_description'] = $_data['description'];
            $_sv['soundcloud_title_url']   = jrCore_url_string($_sv['soundcloud_title']);
            if (isset($_data['artwork_url']) && jrCore_checktype($_data['artwork_url'], 'url')) {
                $_sv['soundcloud_artwork_url'] = $_data['artwork_url'];
            }
            elseif (isset($_data['user']['avatar_url']) && jrCore_checktype($_data['user']['avatar_url'], 'url')) {
                $_sv['soundcloud_artwork_url'] = $_data['user']['avatar_url'];
            }
            else {
                $_sv['soundcloud_artwork_url'] = '';
            }
            $scid = jrCore_db_create_item('jrSoundCloud', $_sv);

            // See if we are creating tags
            if (isset($_data['tag_list']) && strlen($_data['tag_list']) > 0 && isset($_post["import_tags_{$soundcloud_id}"])) {

                // Tags can come in as single words or "quoted strings"
                // [tag_list] => miguel girls simplethings "girls soundtrack" "girls: volumer 2: all adventurous women do" "miguel music"
                $_tags = explode(' ', trim($_data['tag_list']));
                if ($_tags && is_array($_tags)) {
                    $beg = '';
                    $add = true;
                    foreach ($_tags as $tag) {
                        $tag = trim($tag);
                        if ($add && strpos(' ' . $tag, '"')) {
                            // We have opening word of multi-word tag
                            $beg = str_replace('"', '', $tag);
                            $add = false;
                        }
                        elseif (!$add && strlen($beg) > 0) {
                            if (strpos($tag, '"')) {
                                // End of tag
                                $beg .= ' ' . str_replace('"', '', $tag);
                                $tag = $beg;
                                $beg = '';
                                $add = true;
                            }
                            else {
                                $beg .= ' ' . $tag;
                            }
                        }
                        else {
                            $add = true;
                        }
                        if ($add) {
                            $_tmp = array(
                                'tag_text'         => strip_tags($tag),
                                'tag_url'          => jrCore_url_string($tag),
                                'tag_module'       => 'jrSoundCloud',
                                'tag_item_id'      => $scid,
                                'tag_profile_id'   => $_user['_profile_id'],
                                'tag_item_created' => 'UNIX_TIMESTAMP()'
                            );
                            jrCore_db_create_item('jrTags', $_tmp);
                        }
                    }
                }
            }

            // Add the FIRST TRACK to our actions...
            if (!isset($action_saved)) {
                // Add to Actions...
                jrCore_run_module_function('jrAction_save', 'search', 'jrSoundCloud', $scid);
                $action_saved = true;
            }
            $ctr++;
        }
    }

    $ss = urlencode($_post['search']);
    if ($_post['current_search'] != $_post['search']) {
        $_post['page'] = 1;
    }
    jrCore_form_delete_session();
    jrProfile_reset_cache();
    if ($ctr > 0) {
        jrCore_set_form_notice('success', "{$ctr} {$_lang['jrSoundCloud'][44]}");
    }
    $sc = (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) ? (int) $_COOKIE['jrcore_pager_rows'] : 10;
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/search/{$ss}/{$_post['search_area']}/{$sc}/{$_post['search_order']}/p={$_post['page']}");
}

//------------------------------
// magic view to show SoundCloud player
// /soundcloud/soundcloud_player/{item_id}/{auto_play}/{show_artwork}/{width}/{height}
// /['module_url']/['option']/['_1']/['_2']/['_3']/['_4']/['_5']
//------------------------------
function view_jrSoundCloud_display_player($_post, $_user, $_conf)
{
    if (!jrCore_checktype($_post['_1'], 'number_nz')) {
        jrCore_notice_page('error', 'invalid soundcloud id');
    }
    $_rt = jrCore_db_get_item('jrSoundCloud', $_post['_1']);
    if (!$_rt || !is_array($_rt)) {
        jrCore_notice_page('error', 'invalid soundcloud id (2)');
    }
    if ($_post['_2'] && $_post['_2'] != 'false') {
        $_post['_2'] = 'TRUE';
    }
    else {
        $_post['_2'] = 'FALSE';
    }
    if (!isset($_post['_3'])) {
        $_post['_3'] = 'TRUE';
    }
    switch (strtolower($_post['_3'])) {
        case 'true':
        case 'false':
            break;
        default:
            $_post['_3'] = 'TRUE';
            break;
    }
    if (!isset($_post['_4']) || strlen($_post['_4']) < 2 || strlen($_post['_4']) > 4) {
        $_post['_4'] = '100%';
    }
    if (!isset($_post['_5']) || strlen($_post['_4']) > 4) {
        $_post['_5'] = 166;
    }
    $_post['_5'] = intval($_post['_5']);
    jrCore_counter('jrSoundCloud', $_post['_1'], 'soundcloud_stream');
    return jrSoundCloud_get_player($_rt['soundcloud_id'], $_post['_2'], $_post['_3'], $_post['_4'], $_post['_5']);
}

//------------------------------
// integrity_check
//------------------------------
function view_jrSoundCloud_integrity_check($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrSoundCloud');
    jrCore_page_banner("Integrity Check");
    jrCore_page_note('Checks all uploaded SoundCloud tracks to see if they still exist on soundcloud.com. If not, they are deleted.<br>Please be patient - on systems with many SoundCloud tracks, this could take a long time to run.');

    // Form init
    $_tmp = array(
        'submit_value' => 'run SoundCloud integrity check',
        'cancel'       => 'referrer',
        'submit_modal' => 'update',
        'modal_width'  => 600,
        'modal_height' => 400,
        'modal_note'   => 'SoundCloud Integrity Check'
    );
    jrCore_form_create($_tmp);

    // Validate Skins
    $_tmp = array(
        'name'  => 'dummy',
        'type'  => 'hidden',
        'value' => 'on'
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// integrity check save
//------------------------------
function view_jrSoundCloud_integrity_check_save($_post, &$_user, &$_conf)
{
    jrUser_master_only();
    jrCore_form_modal_notice('update', "verifying SoundCloud tracks");
    jrCore_form_modal_notice('update', "&nbsp;");

    // Get all uploaded SoundCloud tracks
    $_sp = array(
        'order_by'    => array('_item_id' => 'ASC'),
        'return_keys' => array('soundcloud_id', 'soundcloud_title'),
        'limit'       => 1000000
    );
    $_rt = jrCore_db_search_items('jrSoundCloud', $_sp);
    if (isset($_rt) && isset($_rt['_items']) && count($_rt['_items']) > 0) {
        $checked = 0;
        $deleted = 0;
        foreach ($_rt['_items'] as $_sid) {
            $_tmp = jrSoundCloud_get_data($_sid['soundcloud_id']);
            if (!$_tmp) {
                // No longer found
                jrCore_db_delete_item('jrSoundCloud', $_sid['_item_id']);
                jrCore_form_modal_notice('update', "'{$_sid['soundcloud_title']}' not found on SoundCloud - Deleted");
                $deleted++;
            }
            else {
                jrCore_form_modal_notice('update', "'{$_sid['soundcloud_title']}' OK");
            }
            usleep(100000);
            $checked++;
            if ($checked % 10 == 0) {
                jrCore_form_modal_notice('update', "&nbsp;");
                jrCore_form_modal_notice('update', "{$checked} SoundCloud tracks checked");
                jrCore_form_modal_notice('update', "&nbsp;");
            }
        }
        jrCore_form_modal_notice('update', "&nbsp;");
        jrCore_form_modal_notice('update', "completed verification of {$checked} SoundCloud IDs");
        jrCore_form_modal_notice('update', "{$deleted} SoundCloud tracks deleted");
    }
    else {
        jrCore_form_modal_notice('update', 'No SoundCloud tracks found');
    }
    jrCore_form_delete_session();
    jrCore_form_modal_notice('complete', 'The SoundCloud integrity check successfully completed');
    exit;
}

//---------------------------------------------
// SoundCloud Widget Config Body (loaded via ajax)
//---------------------------------------------
function view_jrSoundCloud_widget_config_body($_post, $_user, $_conf)
{
    jrUser_session_require_login();
    if (!isset($_post['p']) || !jrCore_checktype($_post['p'], 'number_nz')) {
        $_post['p'] = 1;
    }

    $ss      = array();
    $default = true;

    // specific selected id
    if (isset($_post['sel']) && $_post['sel'] !== "false" && $_post['sel'] !== "undefined" && $_post['sel'] !== "") {
        $ss[]    = "_item_id = {$_post['sel']}";
        $default = false;
    }
    // search string
    if (isset($_post['sstr']) && $_post['sstr'] !== "false" && $_post['sstr'] !== "undefined" && $_post['sstr'] !== "") {
        if (strpos($_post['sstr'], ':')) {
            list($k, $v) = explode(':', $_post['sstr']);
            $ss[] = "{$k} = {$v}";
        }
        else {
            $ss[] = "soundcloud_% LIKE %{$_post['sstr']}%";
        }
        $default = false;
    }

    // default list of items
    if ($default) {
        $ss[] = "_profile_id = {$_user['user_active_profile_id']}";
    }

    // Create search params from $_post
    $_sp = array(
        'search'              => $ss,
        'pagebreak'           => 8,
        'page'                => $_post['p'],
        'exclude_jrUser_keys' => true
    );

    $_rt = jrCore_db_search_items('jrSoundCloud', $_sp);
    return jrCore_parse_template('widget_config_body.tpl', $_rt, 'jrSoundCloud');
}
