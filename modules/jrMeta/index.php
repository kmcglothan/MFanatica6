<?php
/**
 * Jamroom Meta Tag Manager module
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
// browse (browse existing)
//------------------------------
function view_jrMeta_browse($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMeta', 'browse');
    jrCore_page_banner('Meta Tag Browser');
    jrCore_get_form_notice();

    $dat             = array();
    $dat[1]['title'] = 'name';
    $dat[1]['width'] = '20%';
    $dat[2]['title'] = 'content';
    $dat[2]['width'] = '50%';
    $dat[3]['title'] = 'location';
    $dat[3]['width'] = '20%';
    $dat[4]['title'] = 'modify';
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = 'delete';
    $dat[5]['width'] = '5%';
    jrCore_page_table_header($dat);

    // Tag location
    $_loc = array(
        's' => 'site pages',
        'p' => 'profile pages',
        'i' => 'item detail pages'
    );

    if (isset($_conf['jrMeta_tagset']) && strlen($_conf['jrMeta_tagset']) > 5) {

        $_conf['jrMeta_tagset'] = jrMeta_get_conf_tags();
        $_rs                    = json_decode($_conf['jrMeta_tagset'], true);
        foreach ($_rs as $k => $_tag) {
            $dat             = array();
            $dat[1]['title'] = $_tag['n'];
            $dat[1]['class'] = 'center';
            $dat[2]['title'] = $_tag['c'];
            $dat[2]['class'] = 'center';
            $dat[3]['title'] = (isset($_loc["{$_tag['l']}"])) ? $_loc["{$_tag['l']}"] : $_tag['l'];
            $dat[3]['class'] = 'center';
            $dat[4]['title'] = jrCore_page_button("d{$k}", 'modify', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/update/id={$k}')");
            $dat[5]['title'] = jrCore_page_button("m{$k}", 'delete', "if(confirm('Are you sure you want to delete this tag?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/delete_save/id={$k}') }");
            jrCore_page_table_row($dat);
        }
    }
    else {
        $dat             = array();
        $dat[1]['title'] = "<p>no meta tags have been created yet - create one below.</p>";
        $dat[1]['class'] = 'center';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();

    // Form init
    $_tmp = array(
        'submit_value'     => 'create new meta tag',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/admin/tools",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // Name
    $_tmp = array(
        'name'     => 'tag_name',
        'label'    => 'tag name',
        'help'     => 'Select the <strong>name</strong> of this new meta tag.<br><br>For more information about valid HTML5 meta tags, visit: <a href="http://www.w3.org/TR/html5/document-metadata.html#attr-meta-name" target="_blank"><u>WC3 - Document Metadata</u></a>',
        'type'     => 'select',
        'options'  => jrMeta_get_valid_names(),
        'required' => true,
        'section'  => 'create a new meta tag'
    );
    jrCore_form_field_create($_tmp);

    // Match
    $_tmp = array(
        'name'     => 'tag_content',
        'label'    => 'tag content',
        'sublabel' => '150 chars or less (recommended)',
        'help'     => 'Enter the content for this new Meta Tag. For SEO purposes it is recommended this value be around 150 characters or less, and ensure the first 25-30 characters are optimized for display on search engines.',
        'type'     => 'textarea',
        'validate' => 'not_empty',
        'style'    => 'height:45px',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Get active skin pages with HEADER includes
    $_tm = glob(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/*.tpl");
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $page) {
            if (!strpos($page, '/profile_') && strpos(file_get_contents($page), 'template="header.tpl"')) {
                $nam        = basename($page);
                $_loc[$nam] = "Skin Template: {$nam}";
            }
        }
    }

    // Get OLD Site Builder pages if active
    if (jrCore_module_is_active('jrMenu')) {
        $_sp = array(
            'ignore_pending' => true,
            'privacy_check'  => false,
            'skip_triggers'  => true,
            'limit'          => 5000
        );
        $_rt = jrCore_db_search_items('jrMenu', $_sp);
        if ($_rt && is_array($_rt['_items'])) {
            foreach ($_rt['_items'] as $item) {
                if (strlen($item['menu_title']) > 0) {
                    $_loc[$item['menu_url']] = "Site Builder: {$item['menu_title']} ({$item['menu_url']})";
                }
                else {
                    $_loc[$item['menu_url']] = "Site Builder: {$item['menu_url']}";
                }
            }
        }
    }

    // Get Site Builder pages if active
    if (jrCore_module_is_active('jrSiteBuilder')) {
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
        $req = "SELECT page_uri, page_title FROM {$tbl} ORDER BY page_uri ASC, page_title ASC";
        $_sb = jrCore_db_query($req, 'page_uri', false, 'page_title');
        if ($_sb && is_array($_sb)) {
            foreach ($_sb as $uri => $ttl) {
                if (strlen($ttl) > 0) {
                    $_loc[$uri] = "Site Builder: {$ttl} ({$uri})";
                }
                else {
                    $_loc[$uri] = "Site Builder: {$uri}";
                }
            }
        }
    }

    $_tmp = array(
        'name'     => 'tag_location',
        'label'    => 'tag location',
        'help'     => 'Select the pages this new Meta Tag will appear on:<br><br><strong>Site Pages</strong> - these meta tags will be present in all site (skin) pages.<br><br><strong>Profile Pages</strong> - valid profile variables can be used in the content - i.e. {$profile_name}, {$profile_url}, etc.<br><br><strong>Item Detail Pages</strong> - valid item AND profile variables can be used in the content - i.e. {$item_title}, {$item_title_url}, etc.<br><br><strong>Skin Template</strong> - selecting a specific Skin Template allows you to override any default Site Pages meta tags and have specific Meta Tags for the selected template.',
        'type'     => 'select',
        'options'  => $_loc,
        'required' => true
    );

    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// browse_save
//------------------------------
function view_jrMeta_browse_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);
    $_rs = array();
    if (isset($_conf['jrMeta_tagset']) && strlen($_conf['jrMeta_tagset']) > 5) {
        $_rs = json_decode($_conf['jrMeta_tagset'], true);
    }
    $_rs[] = array(
        'n' => trim($_post['tag_name']),
        'c' => jrCore_entity_string($_post['tag_content']),
        'l' => $_post['tag_location']
    );
    jrCore_set_setting_value('jrMeta', 'tagset', json_encode($_rs));

    // save to the longer data holder too
    if (jrCore_db_table_exists('jrMeta', 'meta')) {
        $_rs = jrCore_db_escape(json_encode($_rs));
        $tbl = jrCore_db_table_name('jrMeta', 'meta');
        $req = "INSERT INTO {$tbl} (meta_id, meta_json) VALUES (1, '{$_rs}') ON DUPLICATE KEY UPDATE meta_json = '{$_rs}'";
        jrCore_db_query($req);
    }

    jrCore_delete_config_cache();
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The new tag has been successfully created');
    jrCore_location("{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse");
}

//------------------------------
// update
//------------------------------
function view_jrMeta_update($_post, $_user, $_conf)
{
    jrUser_master_only();

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_location('referrer');
    }
    $_rs = array();
    if (isset($_conf['jrMeta_tagset']) && strlen($_conf['jrMeta_tagset']) > 5) {
        $_rs = json_decode($_conf['jrMeta_tagset'], true);
    }
    if (!isset($_rs["{$_post['id']}"])) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_location('referrer');
    }
    $_tg = $_rs["{$_post['id']}"];

    jrCore_page_include_admin_menu();
    jrCore_page_admin_tabs('jrMeta', 'browse');
    jrCore_page_banner('Modify Meta Tag');
    jrCore_get_form_notice();

    // Form init
    $_tmp = array(
        'submit_value'     => 'save changes',
        'cancel'           => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/browse",
        'form_ajax_submit' => false
    );
    jrCore_form_create($_tmp);

    // ID
    $_tmp = array(
        'name'  => 'id',
        'type'  => 'hidden',
        'value' => $_post['id']
    );
    jrCore_form_field_create($_tmp);

    // Name
    $_tmp = array(
        'name'  => 'tag_name',
        'type'  => 'hidden',
        'value' => $_tg['n']
    );
    jrCore_form_field_create($_tmp);
    $_opt = jrMeta_get_valid_names();
    jrCore_page_custom($_opt["{$_tg['n']}"], 'tag name');

    // Match
    $_tmp = array(
        'name'     => 'tag_content',
        'label'    => 'tag content',
        'sublabel' => '150 chars or less (recommended)',
        'help'     => 'Enter the content for this new Meta Tag. For SEO purposes it is recommended this value be around 150 characters or less, and ensure the first 25-30 characters are optimized for display on search engines.',
        'type'     => 'textarea',
        'value'    => $_tg['c'],
        'validate' => 'not_empty',
        'style'    => 'height:45px',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    // Tag location
    $_loc = array(
        's' => 'site pages',
        'p' => 'profile pages',
        'i' => 'item detail pages'
    );

    // Get active skin pages with HEADER includes
    $_tm = glob(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/*.tpl");
    if ($_tm && is_array($_tm)) {
        foreach ($_tm as $page) {
            if (!strpos($page, '/profile_') && strpos(file_get_contents($page), 'template="header.tpl"')) {
                $nam        = basename($page);
                $_loc[$nam] = "Skin Template: {$nam}";
            }
        }
    }

    // Get OLD Site Builder pages if active
    if (jrCore_module_is_active('jrMenu')) {
        $_sp = array(
            'ignore_pending' => true,
            'privacy_check'  => false,
            'skip_triggers'  => true,
            'limit'          => 5000
        );
        $_rt = jrCore_db_search_items('jrMenu', $_sp);
        if ($_rt && is_array($_rt['_items'])) {
            foreach ($_rt['_items'] as $item) {
                if (strlen($item['menu_title']) > 0) {
                    $_loc[$item['menu_url']] = "Site Builder: {$item['menu_title']} ({$item['menu_url']})";
                }
                else {
                    $_loc[$item['menu_url']] = "Site Builder: {$item['menu_url']}";
                }
            }
        }
    }

    // Get Site Builder pages if active
    if (jrCore_module_is_active('jrSiteBuilder')) {
        $tbl = jrCore_db_table_name('jrSiteBuilder', 'page');
        $req = "SELECT page_uri, page_title FROM {$tbl} ORDER BY page_uri ASC, page_title ASC";
        $_sb = jrCore_db_query($req, 'page_uri', false, 'page_title');
        if ($_sb && is_array($_sb)) {
            foreach ($_sb as $uri => $ttl) {
                if (strlen($ttl) > 0) {
                    $_loc[$uri] = "Site Builder: {$ttl} ({$uri})";
                }
                else {
                    $_loc[$uri] = "Site Builder: {$uri}";
                }
            }
        }
    }

    $_tmp = array(
        'name'     => 'tag_location',
        'label'    => 'tag location',
        'help'     => 'Select the location this new Meta Tag will appear - on site pages, on profile pages, or on item detail pages.<br><br><strong>Profile Pages</strong> - valid profile variable can be used in the content - i.e. {$profile_name}, {$profile_url}, etc.<br><br><strong>Item Detail Pages</strong> - valid item AND profile variables can be used in the content - i.e. {$item_title}, {$item_title_url}, etc.',
        'type'     => 'select',
        'options'  => $_loc,
        'value'    => $_tg['l'],
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    jrCore_page_display();
}

//------------------------------
// update_save
//------------------------------
function view_jrMeta_update_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    jrCore_form_validate($_post);

    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_location('referrer');
    }
    $_rs = array();
    if (isset($_conf['jrMeta_tagset']) && strlen($_conf['jrMeta_tagset']) > 5) {
        $_rs = json_decode($_conf['jrMeta_tagset'], true);
    }
    if (!isset($_rs["{$_post['id']}"])) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_location('referrer');
    }
    $_rs["{$_post['id']}"] = array(
        'n' => trim($_post['tag_name']),
        'c' => jrCore_entity_string($_post['tag_content']),
        'l' => $_post['tag_location']
    );
    jrCore_set_setting_value('jrMeta', 'tagset', json_encode($_rs));

    // save to the longer data holder too
    if (jrCore_db_table_exists('jrMeta', 'meta')) {
        $_rs = jrCore_db_escape(json_encode($_rs));
        $tbl = jrCore_db_table_name('jrMeta', 'meta');
        $req = "INSERT INTO {$tbl} (meta_id, meta_json) VALUES (1, '{$_rs}') ON DUPLICATE KEY UPDATE meta_json = '{$_rs}'";
        jrCore_db_query($req);
    }

    jrCore_delete_config_cache();
    jrCore_form_delete_session();
    jrCore_set_form_notice('success', 'The tag has been successfully updated');
    jrCore_location('referrer');
}

//------------------------------
// delete
//------------------------------
function view_jrMeta_delete_save($_post, $_user, $_conf)
{
    jrUser_master_only();
    if (!isset($_post['id']) || !jrCore_checktype($_post['id'], 'number_nn')) {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_location('referrer');
    }
    $_rs = array();
    if (isset($_conf['jrMeta_tagset']) && strlen($_conf['jrMeta_tagset']) > 5) {
        $_rs = json_decode($_conf['jrMeta_tagset'], true);
    }
    if (isset($_rs["{$_post['id']}"])) {
        unset($_rs["{$_post['id']}"]);
        $_rs = array_values($_rs);
    }
    else {
        jrCore_set_form_notice('error', 'invalid tag id - please try again');
        jrCore_location('referrer');
    }
    jrCore_set_setting_value('jrMeta', 'tagset', json_encode($_rs));

    // save to the longer data holder too
    if (jrCore_db_table_exists('jrMeta', 'meta')) {
        $_rs = jrCore_db_escape(json_encode($_rs));
        $tbl = jrCore_db_table_name('jrMeta', 'meta');
        $req = "INSERT INTO {$tbl} (meta_id, meta_json) VALUES (1, '{$_rs}') ON DUPLICATE KEY UPDATE meta_json = '{$_rs}'";
        jrCore_db_query($req);
    }

    jrCore_delete_config_cache();
    jrCore_location('referrer');
}
