<?php
/**
 * Jamroom RSS Feed and Reader module
 *
 * copyright 2017 The Jamroom Network
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

//------------------------------
// create
//------------------------------
function view_jrFeed_create($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrFeed');
    jrCore_page_banner('Browse RSS Feeds');
    jrCore_page_search('Search Feeds', "{$_conf['jrCore_base_url']}/{$_post['module_url']}/create");

    $dat             = array();
    $dat[1]['title'] = 'Feed Name';
    $dat[1]['width'] = '25%;';
    $dat[2]['title'] = 'Feed URL';
    $dat[2]['width'] = '35%;';
    $dat[3]['title'] = 'Template Code';
    $dat[3]['width'] = '35%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%;';
    jrCore_page_table_header($dat);

    $_sc = array(
        'order_by'      => array('_item_id' => 'desc'),
        'skip_triggers' => true,
        "pagebreak"     => 12,
        'page'          => (isset($_post['p']) && jrCore_checktype($_post['p'], 'number_nz')) ? (int) $_post['p'] : 1
    );
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
        $_sc['search'] = array("feed_name like %{$_post['search_string']}% || feed_url like %{$_post['search_string']}%");
    }
    $_rt = jrCore_db_search_items('jrFeed', $_sc);
    if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
        foreach ($_rt['_items'] as $k => $_f) {
            $dat             = array();
            $dat[1]['title'] = $_f['feed_name'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = "<a href=\"{$_f['feed_url']}\" target=\"_blank\">{$_f['feed_url']}</a>";
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = '{jrFeed_list name="' . $_f['feed_name'] . '"}';
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("u{$k}", 'modify', "location.href='{$_conf['jrCore_base_url']}/feed/update/id={$_f['_item_id']}'");
            jrCore_page_table_row($dat);
        }
        jrCore_page_table_pager($_rt);
    }
    else {
        $dat = array();
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {
            $dat[1]['title'] = "<b>No RSS feeds match your search criteria</b>";
        }
        else {
            $dat[1]['title'] = "<b>No RSS feeds have been created</b>";
        }
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value' => 'create',
        'cancel'       => 'referrer'
    );
    jrCore_form_create($_tmp);

    // Feed Name
    $_tmp = array(
        'name'     => 'feed_name',
        'label'    => 'Feed Name',
        'help'     => 'Unique name for this feed so that it can be searched',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => false,
        'section'  => 'Add a New Feed'
    );
    jrCore_form_field_create($_tmp);

    // Feed URL
    $_tmp = array(
        'name'     => 'feed_url',
        'label'    => 'Feed URL',
        'help'     => 'The full URL of this feed',
        'type'     => 'text',
        'validate' => 'url',
        'required' => false,
        'section'  => 'Add a New Feed'
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// create_save
//------------------------------
function view_jrFeed_create_save($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_rt = jrCore_form_get_save_data('jrFeed', 'create', $_post);
    // See that it is a new feed
    $_s  = array(
        "search" => array(
            "feed_name = {$_rt['feed_name']}"
        )
    );
    $_ft = jrCore_db_search_items('jrFeed', $_s);
    if (isset($_ft['_items'][0]) && is_array($_ft['_items'][0])) {
        jrCore_set_form_notice('error', 'That feed name already exists - please enter another');
        jrCore_form_result("{$_conf['jrCore_base_url']}/feed/update/id={$_ft['_items'][0]['_item_id']}");
    }

    // Check data
    if ($_rt['feed_name'] == '') {
        jrCore_set_form_notice('error', 'Invalid feed name');
        jrCore_form_result("{$_conf['jrCore_base_url']}/feed/create");
    }
    if (!jrCore_checktype($_rt['feed_url'], 'url')) {
        jrCore_set_form_notice('error', 'Invalid feed url');
        jrCore_form_result("{$_conf['jrCore_base_url']}/feed/create");
    }
    unset($_rt['feed_selected']);

    // $id will be the INSERT_ID (_item_id) of the created item
    $id = jrCore_db_create_item('jrFeed', $_rt);
    if (!$id) {
        jrCore_set_form_notice('error', 'Something went wrong creating the datastore item');
        jrCore_form_result();
    }
    jrCore_form_delete_session();
    jrUser_reset_cache($_user['_user_id']);
    jrCore_form_result("{$_conf['jrCore_base_url']}/feed/create");
}

//------------------------------
// update
//------------------------------
function view_jrFeed_update($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();

    // We should get an id on the URL
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_notice_page('error', 'Invalid item ID');
    }
    $_rt = jrCore_db_get_item('jrFeed', $_post['id'], true);
    if (!$_rt) {
        jrCore_notice_page('error', 'Something went wrong retrieving the datastore item');
    }
    // Make sure the calling user has permission to edit this item
    if (!jrUser_can_edit_item($_rt)) {
        jrUser_not_authorized();
    }

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrFeed');
    $tmp = jrCore_page_button('delete', 'Delete this Feed', "if (confirm('Are you sure you want to delete this Feed?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/feed/delete/id={$_post['id']}' )}");
    jrCore_page_banner('RSS Feed Reader - Update', $tmp);

    // Form init
    $_tmp = array(
        'submit_value' => 'save changes',
        'cancel'       => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/create",
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

    // original feed name
    $_tmp = array(
        'name'     => 'feed_original_name',
        'type'     => 'hidden',
        'value'    => $_rt['feed_name'],
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // Feed Name
    $_tmp = array(
        'name'     => 'feed_name',
        'label'    => 'Feed Name',
        'help'     => 'Unique name for this feed so that it can be searched',
        'type'     => 'text',
        'validate' => 'printable',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Feed URL
    $_tmp = array(
        'name'     => 'feed_url',
        'label'    => 'Feed URL',
        'help'     => 'The full URL of this feed',
        'type'     => 'text',
        'validate' => 'url',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Display page with form in it
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrFeed_update_save($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();
    jrCore_form_validate($_post);

    // Make sure we get a good _item_id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item ID');
        jrCore_form_result('referrer');
    }

    // Get data
    $_rt = jrCore_db_get_item('jrFeed', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        // Item does not exist....
        jrCore_set_form_notice('error', 'Something went wrong retrieving the datastore item');
        jrCore_form_result('referrer');
    }

    // Get our posted data - the jrCore_form_get_save_data function will
    // return just those fields that were presented in the form.
    $_sv = jrCore_form_get_save_data('jrFeed', 'update', $_post);

    // See that it is a new feed
    if ($_sv['feed_original_name'] != $_sv['feed_name']) {
        $_s  = array("search" => array("feed_name = {$_post['feed_name']}"));
        $_ft = jrCore_db_search_items('jrFeed', $_s);
        if (isset($_ft['_items'][0]) && is_array($_ft['_items'][0])) {
            jrCore_set_form_notice('error', 'That feed name already exists');
            jrCore_form_result("{$_conf['jrCore_base_url']}/feed/update/id={$_ft['_items'][0]['_item_id']}");
        }
    }
    unset($_sv['feed_original_name']);

    // Check data
    if ($_sv['feed_name'] == '') {
        jrCore_set_form_notice('error', 'Invalid feed name');
        jrCore_form_result("{$_conf['jrCore_base_url']}/feed/update/id={$_post['id']}");
    }
    if (!jrCore_checktype($_sv['feed_url'], 'url')) {
        jrCore_set_form_notice('error', 'Invalid feed url');
        jrCore_form_result("{$_conf['jrCore_base_url']}/feed/update/id={$_post['id']}");
    }

    // Save all updated fields to the Data Store
    jrCore_db_update_item('jrFeed', $_post['id'], $_sv);
    jrCore_form_delete_session();

    jrUser_reset_cache($_user['_user_id']); //make sure the list displays the updated version

    //clear the cache so that on the /update/id=16 page the get item gets the newest item. (will get the old item name without this.)
    $key = "jrFeed-{$_post['id']}-1";
    jrCore_delete_cache('jrFeed', $key, false);

    jrCore_form_result("{$_conf['jrCore_base_url']}/feed/create");
}

//------------------------------
// delete
//------------------------------
function view_jrFeed_delete($_post, $_user, $_conf)
{
    // Admin only
    jrUser_master_only();
    jrCore_validate_location_url();

    // Make sure we get a good id
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nz')) {
        jrCore_set_form_notice('error', 'Invalid item ID');
        jrCore_form_result();
    }
    $_rt = jrCore_db_get_item('jrFeed', $_post['id']);
    if (!isset($_rt) || !is_array($_rt)) {
        jrCore_set_form_notice('error', 'Invalid item ID');
        jrCore_form_result();
    }
    // Delete item
    jrCore_db_delete_item('jrFeed', $_post['id']);
    jrUser_reset_cache($_user['_user_id']);
    jrCore_form_result("{$_conf['jrCore_base_url']}/{$_post['module_url']}/create");
}

//-------------------------------
// http://site.com/feed/audio/limit=10
// http://site.com/feed/audio/brian/limit=10
//-------------------------------
function view_jrFeed_default($_post, $_user, $_conf)
{
    global $_urls;
    // Our module will come in as option
    $mod = (isset($_urls["{$_post['option']}"])) ? $_urls["{$_post['option']}"] : false;
    if (!$mod) {
        jrCore_notice_page('error', 'invalid module');
    }
    if (!jrCore_module_is_active($mod)) {
        jrCore_notice_page('error', 'module is not active');
    }

    // Has this module registered for feed support?
    $_rm = jrCore_get_registered_module_features('jrFeed', 'feed_support');
    if (!$_rm || !isset($_rm[$mod])) {
        jrCore_notice_page('error', 'module has not registered for RSS Feed support');
    }

    $ckey = json_encode($_post);
    if (!$_rt = jrCore_is_cached('jrFeed', $ckey)) {

        // Add in any search post search parameters
        $pfx = jrCore_db_get_prefix($mod);
        if ($pfx) {

            // Get our results
            $_sp = array(
                'limit'                        => (isset($_post['limit']) && jrCore_checktype($_post['limit'], 'number_nz') && $_post['limit'] <= 100) ? (int) $_post['limit'] : 25,
                'order_by'                     => array(
                    '_item_id' => 'desc'
                ),
                'exclude_jrProfile_quota_keys' => true
            );

            // We can get a profile as $_1
            if (isset($_post['_1']) && strlen($_post['_1']) > 0) {
                if ($_prof = jrCore_db_get_item_by_key('jrProfile', 'profile_url', $_post['_1'], true)) {
                    $_sp['search'] = array("_profile_id = {$_prof['_profile_id']}");
                }
                else {
                    jrCore_add_to_cache('jrFeed', $ckey, array('_items' => array()));
                    jrCore_notice_page('error', 'profile data found');
                }
            }

            foreach ($_post as $k => $v) {
                if (strpos($k, "{$pfx}_") === 0) {
                    $_sp['search'][] = "{$k} = {$v}";
                }
            }

            // Get Items
            $_rt = jrCore_db_search_items($mod, $_sp);
            if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
                jrCore_add_to_cache('jrFeed', $ckey, array('_items' => array()));
                jrCore_notice_page('error', 'no data found');
            }

            // Default - simple format
            $pfx = jrCore_db_get_prefix($mod);
            $url = jrCore_get_module_url($mod);
            foreach ($_rt['_items'] as $k => $_itm) {
                switch ($mod) {
                    case 'jrProfile':
                        $_rt['_items'][$k]['title']       = '@' . $_itm['profile_url'];
                        $_rt['_items'][$k]['link']        = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}";
                        $_rt['_items'][$k]['description'] = (isset($_itm['profile_bio']{0})) ? jrCore_replace_emoji(jrCore_strip_html(smarty_modifier_jrCore_format_string($_itm['profile_bio'], 0))) : '';
                        break;
                    case 'jrUser':
                        $_rt['_items'][$k]['title']       = $_itm['user_name'] . ' - @' . $_itm['profile_url'];
                        $_rt['_items'][$k]['link']        = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}";
                        $_rt['_items'][$k]['description'] = (isset($_itm['profile_bio']{0})) ? jrCore_replace_emoji(jrCore_strip_html(smarty_modifier_jrCore_format_string($_itm['profile_bio'], 0))) : '';
                        break;
                    default:
                        if (isset($_itm["{$pfx}_title"])) {
                            $_rt['_items'][$k]['title'] = $_itm["{$pfx}_title"] . ' - @' . $_itm['profile_url'];
                        }
                        else {
                            $_rt['_items'][$k]['title'] = '@' . $_itm['profile_url'];
                        }
                        $_rt['_items'][$k]['link'] = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$url}/{$_itm['_item_id']}" . ((isset($_itm["{$pfx}_title_url"])) ? "/{$_itm["{$pfx}_title_url"]}" : '');
                        $_rt['_items'][$k]['guid'] = "{$_conf['jrCore_base_url']}/{$_itm['profile_url']}/{$url}/{$_itm['_item_id']}";
                        $description               = false;
                        if (isset($_itm["{$pfx}_description"])) {
                            $description = $_itm["{$pfx}_description"];
                        }
                        elseif (isset($_itm["{$pfx}_text"])) {
                            $description = $_itm["{$pfx}_text"];
                        }
                        elseif (isset($_itm["{$pfx}_body"])) {
                            $description = $_itm["{$pfx}_body"];
                        }
                        $_rt['_items'][$k]['description'] = ($description) ? jrCore_replace_emoji(jrCore_strip_html(smarty_modifier_jrCore_format_string(nl2br($description), 0), 'br')) : '';
                        break;
                }
                $_rt['_items'][$k]['pubdate'] = strftime("%a, %d %b %Y %T %z", $_itm['_created']);
            }

            // Trigger listeners to format results
            $_args = array(
                'module' => $mod,
                'prefix' => $pfx,
                'url'    => $url,
                'limit'  => $_sp['limit']
            );
            if (isset($_post['_1'])) {
                $_args['profile_url'] = $_post['_1'];
            }
            $_rt['_items'] = jrCore_trigger_event('jrFeed', 'create_rss_feed', $_rt['_items'], $_args);

            // Main RSS info
            $_ln             = jrUser_load_lang_strings();
            $_rt['rss_desc'] = '';
            if (isset($_post['_1']) && strlen($_post['_1']) > 0) {
                $_rt['rss_title'] = '@' . $_rt['_items'][0]['profile_name'] . ' - ' . $_ln[$mod]['menu'];
                $_rt['rss_url']   = "{$_conf['jrCore_base_url']}/{$_post['_1']}";
                if (isset($_rt['_items'][0]['profile_bio']) && strlen($_rt['_items'][0]['profile_bio']) > 0) {
                    $_rt['rss_desc'] = jrCore_strip_html(str_replace(array('<p>', '</p>'), '', $_rt['_items'][0]['profile_bio']));
                }
            }
            else {
                $_rt['rss_title'] = $_ln[$mod]['menu'];
                $_rt['rss_url']   = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$_post['option']}";
            }
        }
        $_rt['rss_builddate'] = strftime("%a, %d %b %Y %T %z", time());
        $_rt['rss_feed_url']  = jrCore_get_current_url();
        jrCore_add_to_cache('jrFeed', $ckey, $_rt);
    }

    // Do we have a custom template?
    if (is_file(APP_DIR . "/modules/{$mod}/templates/item_rss.tpl") || is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$mod}_item_rss.tpl")) {
        $rss = jrCore_parse_template('item_rss.tpl', $_rt, $mod);
    }
    else {
        $rss = jrCore_parse_template('rss.tpl', $_rt, 'jrFeed');
    }

    jrCore_set_custom_header('Expires: ' . gmdate('D, d M Y H:i:s', (time() + 3600)) . ' GMT');
    jrCore_set_custom_header('Content-Type: application/xml; charset=utf-8');
    return $rss;
}
