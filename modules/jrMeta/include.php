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

/**
 * meta
 */
function jrMeta_meta()
{
    $_tmp = array(
        'name'        => 'Meta Tag Manager',
        'url'         => 'metatag',
        'version'     => '1.1.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Create and insert Meta Tags into site and profile pages',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/2914/meta-tag-manager',
        'license'     => 'mpl',
        'category'    => 'tools'
    );
    return $_tmp;
}

/**
 * init
 */
function jrMeta_init()
{
    jrCore_register_module_feature('jrCore', 'tool_view', 'jrMeta', 'browse', array('meta tag browser', 'Browse, Create, Update, and Delete Meta Tags'));
    jrCore_register_module_feature('jrCore', 'admin_tab', 'jrMeta', 'browse', 'Meta Tag Browser');

    // Our default master view
    jrCore_register_module_feature('jrCore', 'default_admin_view', 'jrMeta', 'browse');

    jrCore_register_event_listener('jrCore', 'index_template', 'jrMeta_insert_tags_listener');
    jrCore_register_event_listener('jrCore', 'skin_template', 'jrMeta_insert_tags_listener');
    jrCore_register_event_listener('jrCore', 'module_view', 'jrMeta_insert_tags_listener');

    jrCore_register_event_listener('jrProfile', 'profile_view', 'jrMeta_insert_tags_listener');
    jrCore_register_event_listener('jrProfile', 'item_detail_view', 'jrMeta_insert_tags_listener');
    jrCore_register_event_listener('jrProfile', 'item_index_view', 'jrMeta_insert_tags_listener');
    jrCore_register_event_listener('jrProfile', 'item_list_view', 'jrMeta_insert_tags_listener');

    jrCore_register_event_listener('jrSiteBuilder', 'page_content', 'jrMeta_insert_tags_listener');

    jrCore_register_event_listener('jrCore', 'view_results', 'jrMeta_view_results_listener');

    // add a button to the site builder page settings to get to our meta tag browser
    jrCore_register_event_listener('jrCore', 'form_display', 'jrMeta_form_display_listener');

    return true;
}

//-------------------------
// EVENT LISTENERS
//-------------------------

/**
 * Insert Meta Tags
 * @param $_data array incoming data array
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrMeta_insert_tags_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (jrCore_is_view_request()) {

        $_tmp = jrCore_get_registered_module_features('jrUser', 'skip_session');
        if ($_tmp && is_array($_tmp)) {
            foreach ($_tmp as $mod => $_opts) {
                if (isset($_post['option']) && isset($_opts["{$_post['option']}"]) && ($mod == $_post['module'] || $_opts["{$_post['option']}"] == 'magic_view')) {
                    return $_data;
                }
            }
        }
        if (isset($_conf['jrMeta_tagset']{5})) {

            $_conf['jrMeta_tagset'] = jrMeta_get_conf_tags();
            $_rs                    = json_decode($_conf['jrMeta_tagset'], true);
            if ($_rs && is_array($_rs)) {

                // Check for Meta Tags on a specific template
                $_tg = array();

                // Check for template specific tags
                if ($event == 'index_template') {
                    foreach ($_rs as $k => $v) {
                        if ($v['l'] == '/') {
                            $_tg["{$v['n']}"] = jrCore_entity_string($v['c']);
                            unset($_rs[$k]);
                        }
                        elseif ($v['l'] == 'index.tpl' && !isset($_tg["{$v['n']}"])) {
                            $_tg["{$v['n']}"] = jrCore_entity_string($v['c']);
                            unset($_rs[$k]);
                        }
                    }
                }
                elseif ($event == 'skin_template') {
                    foreach ($_rs as $k => $v) {
                        if (strpos($v['l'], '.tpl') && $v['l'] == $_args['template_name']) {
                            $_tg["{$v['n']}"] = jrCore_entity_string($v['c']);
                            unset($_rs[$k]);
                        }
                    }
                }
                elseif ($event == 'page_content') {
                    /* site builder */
                    foreach ($_rs as $k => $v) {
                        if ($v['l'] == $_args['page_uri']) {
                            $_tg["{$v['n']}"] = jrCore_entity_string($v['c']);
                            unset($_rs[$k]);
                        }
                    }
                }
                elseif ($event == 'module_view' && (!isset($_post['option']) || strlen($_post['option']) === 0)) {
                    // Is this a module index?
                    foreach ($_rs as $k => $v) {
                        if ($v['l'] == "{$_post['module']}_index.tpl") {
                            $_tg["{$v['n']}"] = jrCore_entity_string($v['c']);
                            unset($_rs[$k]);
                        }
                    }
                }

                // General rules
                if (count($_rs) > 0) {
                    foreach ($_rs as $v) {

                        // name could have return chars
                        $v['n'] = trim($v['n']);

                        // Check location
                        switch ($v['l']) {

                            // Item Detail View
                            case 'i':
                                if ($event == 'item_detail_view') {
                                    $_rp = array();
                                    $pfx = jrCore_db_get_prefix($_args['module']);
                                    foreach ($_data as $pk => $pv) {
                                        if (is_string($pv) && !strpos($pv, 'jrEmbed')) {
                                            // Check for HTML/BBCode
                                            if (!is_numeric($pv) && (strpos(' ' . $pv, '<') || strpos(' ' . $pv, '['))) {
                                                $pv = strip_tags(smarty_modifier_jrCore_format_string($pv, $_data['profile_quota_id']));
                                            }
                                            $pk       = str_replace("{$pfx}_", 'item_', $pk);
                                            $_rp[$pk] = trim($pv);
                                        }
                                    }
                                    $_tg["{$v['n']}"] = jrCore_entity_string(trim(jrCore_parse_template($v['c'], $_rp, 'jrMeta')));
                                    jrCore_set_flag('item_detail_view_processed', 1);
                                }
                                break;

                            // profile page only - we include $rep vars here
                            case 'p':
                                if ($event == 'profile_view' && !jrCore_get_flag('item_detail_view_processed')) {
                                    $_rp = array();
                                    foreach ($_data as $pk => $pv) {
                                        if (is_string($pv)) {
                                            // Check for HTML/BBCode
                                            if (!is_numeric($pv) && (strpos(' ' . $pv, '<') || strpos(' ' . $pv, '['))) {
                                                $pv = strip_tags(smarty_modifier_jrCore_format_string($pv, $_data['profile_quota_id']));
                                            }
                                            $_rp[$pk] = trim($pv);
                                        }
                                    }
                                    $_tg["{$v['n']}"] = jrCore_entity_string(trim(jrCore_parse_template($v['c'], $_rp, 'jrMeta')));
                                }
                                break;

                            // site page
                            case 's':
                                if ($event != 'profile_view' && $event != 'item_detail_view' && !isset($_tg["{$v['n']}"])) {
                                    $_tg["{$v['n']}"] = jrCore_entity_string($v['c']);
                                }
                                break;

                        }

                    }
                }
                if (count($_tg) > 0) {
                    jrCore_create_page_element('meta', $_tg);
                }
            }
        }

        // process the meta.tpl files for profile pages
        if (isset($_args['module']) && jrCore_module_is_active($_args['module'])) {

            $file = false;
            switch ($event) {
                case 'item_detail_view':
                    $file = 'item_detail_meta.tpl';
                    break;
                case 'item_list_view':
                    $file = 'item_list_meta.tpl';
                    break;
                case 'item_index_view':
                    $file = 'item_index_meta.tpl';
                    break;
            }
            if ($file && is_file(APP_DIR . "/modules/{$_args['module']}/templates/{$file}")) {
                $_rep = array(
                    'item'   => $_data,
                    'method' => jrCore_get_server_protocol()
                );
                $html = trim(jrCore_parse_template($file, $_rep, $_args['module']));
                if ($html && strlen($html) > 0) {
                    jrCore_set_flag('meta_html', $html);
                }
            }
        }

    }
    return $_data;
}

/**
 * Add Meta Tags support to Site Builder pages
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrMeta_form_display_listener($_data, $_user, $_conf, $_args, $event)
{
    if ($_data['form_view'] == 'jrSiteBuilder/modify_page_settings') {
        $murl = jrCore_get_module_url('jrMeta');
        $html = jrCore_page_button('mt', 'Add meta tags to this page', "window.open('{$_conf['jrCore_base_url']}/{$murl}/browse');");
        $_tmp = array(
            'module' => 'jrMeta',
            'name'   => 'jrmeta_add_tags',
            'label'  => 'Meta Tags',
            'help'   => 'Open the meta tag browser to add tags to this page.',
            'html'   => $html
        );
        jrCore_form_field_custom_display($_tmp);
    }
    return $_data;
}

/**
 * meta tags as html inject into header
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrMeta_view_results_listener($_data, $_user, $_conf, $_args, $event)
{
    if (strpos($_data, '</head>')) {
        if ($html = jrCore_get_flag('meta_html')) {
            jrCore_delete_flag('meta_html');
            return str_replace('</head>', trim($html) . "\n</head>", $_data);
        }
    }
    return $_data;
}

//-------------------------
// FUNCTIONS
//-------------------------

/**
 * Strip all HTML from a string
 * @param $str string to strip HTML from
 * @return string
 */
function jrMeta_strip_all_html($str)
{
    $str = smarty_modifier_jrCore_format_string($str, 'allow_all_formatters');
    return preg_replace('!\s+!', ' ', str_replace(array("\r", "\n"), ' ', jrCore_strip_html($str)));
}

/**
 * Get configured Meta Tags
 * @return mixed
 */
function jrMeta_get_conf_tags()
{
    global $_conf;
    // Only bring in our longer meta tags if needed
    if (isset($_conf['jrMeta_tagset']) && strlen($_conf['jrMeta_tagset']) > 4000 && jrCore_db_table_exists('jrMeta', 'meta')) {
        $tbl = jrCore_db_table_name('jrMeta', 'meta');
        $req = "SELECT meta_json FROM {$tbl} LIMIT 1";
        $_rt = jrCore_db_query($req, 'SINGLE');
        if ($_rt && is_array($_rt) && strlen($_rt['meta_json']) > 1) {
            $_conf['jrMeta_tagset'] = $_rt['meta_json'];
        }
    }
    return $_conf['jrMeta_tagset'];
}

/**
 * Get active list of Meta Tags
 * @return array
 */
function jrMeta_get_valid_names()
{
    global $_conf;
    $_out = array();
    if (isset($_conf['jrMeta_names']{2})) {
        $_opt = explode("\n", $_conf['jrMeta_names']);
        if ($_opt && is_array($_opt)) {
            foreach ($_opt as $v) {
                $v = trim($v);
                if (strlen($v) > 0) {
                    $_out[$v] = $v;
                }
            }
        }
    }
    // Add in defaults
    $_tmp = jrMeta_get_default_names();
    if ($_tmp) {
        foreach ($_tmp as $k => $v) {
            if (!isset($_out[$k])) {
                $_out[$k] = $v;
            }
        }
    }
    natcasesort($_out);
    return $_out;
}

/**
 * Get valid meta tag names for Global Config
 * @return string
 */
function jrMeta_get_valid_names_for_config()
{
    return implode("\n", jrMeta_get_valid_names());
}

/**
 * Get list of default Meta name tags
 * @return array
 */
function jrMeta_get_default_names()
{
    $_tags = array(
        'abstract'                 => 'abstract',
        'application-name'         => 'application-name',
        'author'                   => 'author',
        'copyright'                => 'copyright',
        'description'              => 'description',
        'distribution'             => 'distribution',
        'generator'                => 'generator',
        'google-site-verification' => 'google-site-verification',
        'keywords'                 => 'keywords',
        'language'                 => 'language',
        'rating'                   => 'rating',
        'revisit-after'            => 'revisit-after',
        'rights-standard'          => 'rights-standard',
        'robots'                   => 'robots',
        'web_author'               => 'web_author',
        'og:title'                 => 'og:title',
        'og:type'                  => 'og:type',
        'og:url'                   => 'og:url',
        'og:description'           => 'og:description',
        'og:determiner'            => 'og:determiner',
        'og:locale'                => 'og:locale',
        'og:site_name'             => 'og:site_name',
        'og:image'                 => 'og:image',
        'og:image:width'           => 'og:image:width',
        'og:image:height'          => 'og:image:height',
        'og:audio'                 => 'og:audio',
        'og:video'                 => 'og:video',
        'twitter:card'             => 'twitter:card',
        'twitter:site'             => 'twitter:site',
        'twitter:site:id'          => 'twitter:site:id',
        'twitter:creator'          => 'twitter:creator',
        'twitter:creator:id'       => 'twitter:creator:id',
        'twitter:description'      => 'twitter:description',
        'twitter:title'            => 'twitter:title',
        'twitter:image'            => 'twitter:image',
        'twitter:image:alt'        => 'twitter:image:alt',
        'twitter:player'           => 'twitter:player',
        'twitter:player:width'     => 'twitter:player:width',
        'twitter:player:height'    => 'twitter:player:height',
        'twitter:player:stream'    => 'twitter:player:stream'
    );
    natcasesort($_tags);
    return $_tags;
}

