<?php
/**
 * Jamroom Documentation module
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
// profile_default
//------------------------------
function profile_view_jrDocs_default($_profile, $_post, $_user, $_conf)
{
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return false;
    }
    // [_uri] => /brian/documentation/faq/25/doc-title
    // [module_url] => brian
    // [option] => documentation
    // [_1] => faq
    // [_2] => 25
    // [_3] => doc-title
    // [_profile_id] => 1

    // If $_1 comes in as a NUMBER, and $_2 is NOT a number, fix URL
    if (isset($_post['_1']) && jrCore_checktype($_post['_1'], 'number_nz') && isset($_post['_2']) && !jrCore_checktype($_post['_2'], 'number_nz')) {

        // See if we have a matching document
        $_rt = jrCore_db_get_item('jrDocs', $_post['_1']);
        if (!$_rt || $_rt['doc_title_url'] != $_post['_2'] || $_rt['_profile_id'] != $_profile['_profile_id']) {
            jrCore_page_not_found();
        }
        // Check for doc_category...
        if (isset($_rt['doc_category_url']) && strlen($_rt['doc_category_url']) > 0) {
            $url = jrCore_get_module_url('jrDocs');
            header('HTTP/1.1 301 Moved Permanently');
            jrCore_location("{$_conf['jrCore_base_url']}/{$_profile['profile_url']}/{$url}/{$_rt['doc_category_url']}/{$_post['_1']}/{$_rt['doc_title_url']}");
        }
    }

    $out = '';
    if (isset($_post['_2']) && jrCore_checktype($_post['_2'], 'number_nz')) {

        // Check for Cache
        $key = md5($_post['_uri']);
        $_ch = jrCore_is_cached('jrDocs', $key);
        if (!$_ch || strlen($_ch['html']) === 0) {

            // We're viewing a specific document ID
            // Go get all parts involved with this document
            $_sp = array(
                'search'              => array(
                    "_profile_id = {$_profile['_profile_id']}",
                    "doc_group_id = {$_post['_2']}"
                ),
                'order_by'            => array(
                    'doc_section_order' => 'numerical_asc'
                ),
                'limit'               => 200,
                'exclude_jrUser_keys' => true
            );
            $_rt = jrCore_db_search_items('jrDocs', $_sp);
            if (!$_rt || !is_array($_rt) || !isset($_rt['_items'])) {
                jrCore_page_not_found();
            }

            // Make sure our doc_group_id matches...
            if (!isset($_rt['_items'][0]['_item']) || $_rt['_items'][0]['_item_id'] != $_post['_2']) {
                // We have the wrong leader - we need to fix this up
                $_tmp = array();
                foreach ($_rt['_items'] as $k => $v) {
                    if ($v['_item_id'] == $_post['_2']) {
                        // We found our topic header - always comes first - update
                        $_tmp[] = $v;
                        $_up    = array('doc_section_order' => 0);
                        jrCore_db_update_item('jrDocs', $_post['_2'], $_up);
                        unset($_rt['_items'][$k]);
                        break;
                    }
                }
                if (count($_rt['_items']) > 0) {
                    foreach ($_rt['_items'] as $k => $v) {
                        $_tmp[] = $v;
                    }
                }
                $_rt['_items'] = $_tmp;
            }

            // Check doc group
            if (isset($_rt['_items'][0]['doc_group']) && $_rt['_items'][0]['doc_group'] != 'all') {
                if (!jrCore_user_is_part_of_group($_rt['_items'][0]['doc_group'])) {
                    jrCore_page_not_found();
                }
            }

            // Is this profile still the profile owner of this item?
            if ($_rt['_items'][0]['_profile_id'] > 0 && $_rt['_items'][0]['_profile_id'] != $_profile['_profile_id']) {
                // We have been moved..
                $_rt = $_rt['_items'][0];
                $url = jrCore_get_module_url('jrDocs');
                header('HTTP/1.1 301 Moved Permanently');
                jrCore_location("{$_conf['jrCore_base_url']}/{$_rt['profile_url']}/{$url}/{$_post['_2']}/{$_rt['doc_title_url']}");
            }

            // See if we are showing related docs
            if (isset($_rt['_items'][0]['doc_show_related']) && $_rt['_items'][0]['doc_show_related'] == 'off') {
                jrCore_set_flag('jrdocs_hide_show_related', true);
            }

            // First - process header section.  See if we are showing a TOC
            $_rt['_items'][0]['doc_section_count'] = intval(count($_rt['_items']) - 1);
            if (isset($_rt['_items'][0]['doc_show_toc']) && $_rt['_items'][0]['doc_show_toc'] == 'on') {
                // We're building our TOC - process table of contents template
                $tcnt = 0;
                foreach ($_rt['_items'] as $_v) {
                    if (!empty($_v['doc_title_url']) && strlen($_v['doc_title_url']) > 0) {
                        $tcnt++;
                    }
                }
                if ($tcnt >= 3) {
                    $_rt['_items'][0]['doc_table_of_contents'] = jrCore_parse_template("item_table_of_contents.tpl", $_rt, 'jrDocs');
                }
            }

            // Next, create our page jumper
            $dif = ($_rt['_items'][0]['doc_order'] > 2 && $_rt['_items'][0]['doc_order'] != 500) ? ($_rt['_items'][0]['doc_order'] - 2) : 0;

            $_sp = array(
                'search'        => array(
                    "_profile_id = {$_profile['_profile_id']}",
                    "doc_order > {$dif}"
                ),
                'order_by'      => array(
                    'doc_order' => 'numerical_asc'
                ),
                'return_keys'   => array('_item_id', 'doc_order', 'doc_title', 'doc_title_url', 'doc_group'),
                'limit'         => 500,
                'skip_triggers' => true,
                'privacy_check' => false
            );
            if (isset($_rt['_items'][0]['doc_category'])) {
                $_sp['search'][] = 'doc_category = ' . $_rt['_items'][0]['doc_category'];
            }
            $_dc = jrCore_db_search_items('jrDocs', $_sp);
            if ($_dc && is_array($_dc) && isset($_dc['_items'])) {
                $_dc['_items'] = jrDocs_get_docs_visible_to_viewer($_dc['_items'], $_profile['_profile_id']);
                if (is_array($_dc['_items'])) {
                    foreach ($_dc['_items'] as $_v) {
                        if ($_v['_item_id'] == $_post['_2']) {
                            continue;
                        }
                        if ($_v['doc_order'] < $_rt['_items'][0]['doc_order']) {
                            // See if we are closer to our current doc
                            if (!isset($_rt['_items'][0]['_prev']) || $_rt['_items'][0]['_prev']['doc_order'] < $_v['doc_order']) {
                                $_rt['_items'][0]['_prev'] = $_v;
                            }
                            if (isset($_rt['_items'][0]['_next'])) {
                                break;
                            }
                        }
                        elseif ($_v['doc_order'] > $_rt['_items'][0]['doc_order'] && !isset($_rt['_items'][0]['_next'])) {
                            $_rt['_items'][0]['_next'] = $_v;
                            if (isset($_rt['_items'][0]['_prev'])) {
                                break;
                            }
                        }
                    }
                }
            }
            if (isset($_rt['_items'][0]['doc_title'])) {
                jrCore_page_title("{$_rt['_items'][0]['doc_title']} - {$_rt['_items'][0]['profile_name']} ");
            }
            $_rt['_items'][0]['item'] = $_rt['_items'][0];
            $_profile['doc_content']  = jrCore_parse_template("doc_section_header.tpl", $_rt['_items'][0], 'jrDocs');

            // Go through and setup templates for each section
            $_cs = array();
            foreach ($_rt['_items'] as $_doc) {
                switch ($_doc['doc_section_type']) {

                    case 'header':
                    case 'footer':
                        continue 2;
                        break;

                    case 'function_definition':
                        // We need to create our function definition from the params
                        if (isset($_doc['doc_parameters']{1})) {
                            $_doc['doc_parameters'] = json_decode($_doc['doc_parameters'], true);
                            $tmp                    = "<span class=\"doc_function_name\">{$_doc['doc_returns']}&nbsp;<b>{$_doc['doc_title']}</b> (</span><br>";
                            $cnt                    = count($_doc['doc_parameters']);
                            foreach ($_doc['doc_parameters'] as $k => $_prm) {
                                if (isset($_prm['required']) && $_prm['required'] == 'on') {
                                    if (($k + 1) < $cnt) {
                                        $tmp .= "<span class=\"doc_param_required\">{$_prm['type']} {$_prm['name']},</span><br>";
                                    }
                                    else {
                                        $tmp .= "<span class=\"doc_param_required\">{$_prm['type']} {$_prm['name']}</span><br>";
                                    }
                                }
                                else {
                                    if (($k + 1) < $cnt) {
                                        $tmp .= "<span class=\"doc_param\">{$_prm['type']} {$_prm['name']},</span><br>";
                                    }
                                    else {
                                        $tmp .= "<span class=\"doc_param\">{$_prm['type']} {$_prm['name']}</span><br>";
                                    }
                                }
                            }
                            $_doc['doc_function_declaration'] = $tmp . ')';
                        }
                        if (isset($_doc['doc_param_list']) && $_doc['doc_param_list'] == 'off') {
                            $_doc['doc_function_declaration'] = $_doc['doc_title'];
                        }
                        break;

                    case 'code':
                        // We need to bring in our syntax highlighting
                        switch ($_doc['doc_code_language']) {
                            case 'Php':
                                $_doc['doc_syntax_code'] = 'php';
                                break;
                            case 'JScript':
                                $_doc['doc_syntax_code'] = 'javascript';
                                break;
                            case 'Xml':
                                $_doc['doc_syntax_code'] = 'xml';
                                break;
                            case 'Css':
                                $_doc['doc_syntax_code'] = 'css';
                                break;
                        }
                        // Bring in scrivo
                        if (!jrCore_get_flag('jrCore_scrivo_include')) {
                            require_once APP_DIR . '/modules/jrCore/contrib/scrivo/Highlighter.php';
                            require_once APP_DIR . '/modules/jrCore/contrib/scrivo/JsonRef.php';
                            require_once APP_DIR . '/modules/jrCore/contrib/scrivo/Language.php';
                            jrCore_set_flag('jrCore_scrivo_include', 1);
                        }

                        $hl = new Highlight\Highlighter();
                        $_l = array('php', 'javascript', 'css', 'xml');
                        $hl->setAutodetectLanguages($_l);
                        try {
                            $code             = $hl->highlight($_doc['doc_syntax_code'], $_doc['doc_code']);
                            $_doc['doc_code'] = $code->value;
                            $_cs[]            = $_doc['doc_code_language'];
                        }
                        catch (Exception $e) {
                        }
                        break;

                    case 'text_and_image':
                        // For our sections that have an image, in order to make the image look good
                        // on High DPI displays, we need to set a max-width and then use a higher res version
                        $_img = jrImage_get_allowed_image_widths();
                        $size = (isset($_doc['doc_image_display_size'])) ? $_doc['doc_image_display_size'] : 'medium';
                        if (!isset($_img[$size])) {
                            $size = 'medium';
                        }
                        $size = $_img[$size];
                        $_tmp = array_keys($_img);
                        if ($_tmp && is_array($_tmp)) {
                            $next = 1280;
                            foreach ($_img as $k => $s) {
                                if ($s > ($size * 1.5)) {
                                    $next = $k;
                                    break;
                                }
                            }
                            if ($next) {
                                $_doc['doc_image_display_size'] = $next;
                            }
                        }
                        $_doc['doc_image_max_width'] = $size;
                        break;

                }
                $_profile['doc_content'] .= jrCore_parse_template("doc_section_{$_doc['doc_section_type']}.tpl", $_doc, 'jrDocs');
            }
            $_profile['item']        = $_rt['_items'][0];
            $_profile['doc_content'] .= jrCore_parse_template("doc_section_footer.tpl", $_profile, 'jrDocs');
            $out                     = jrCore_parse_template('item_doc_detail.tpl', $_profile, 'jrDocs');
            // meta for detail page
            $_rep = array(
                '_items' => $_rt['_items'],
                'method' => jrCore_get_server_protocol()
            );
            $html = jrCore_parse_template('item_detail_meta.tpl', $_rep, 'jrDocs');
            jrCore_set_flag('meta_html', $html);

            $_ch = array(
                'html'      => $out,
                'code'      => (count($_cs) > 0) ? json_encode($_cs) : '',
                'title'     => $_rt['_items'][0]['doc_title'],
                'meta_html' => $html
            );
            jrCore_add_to_cache('jrDocs', $key, $_ch, 0, $_rt['_items'][0]['_profile_id']);
        }
        else {

            $out = $_ch['html'];
            jrCore_page_title("{$_ch['title']} - {$_profile['profile_name']}");
            jrCore_set_flag('meta_html', $_ch['meta_html']);
            unset($_ch);
        }
    }

    // We're doing a Category
    elseif (isset($_post['_1']) && strlen($_post['_1']) > 0) {

        // See if we were given a search string
        if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {

            $_sp = array(
                'search'        => array(
                    "_profile_id = {$_profile['_profile_id']}",
                    "doc_content like %{$_post['search_string']}%"
                ),
                'group_by'      => 'doc_group_id',
                'limit'         => 50,
                'skip_triggers' => true,
                'return_keys'   => array('doc_group_id', 'doc_content')
            );
            $_rt = jrCore_db_search_items('jrDocs', $_sp);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                $_id = array();
                foreach ($_rt['_items'] as $v) {
                    $gid = (int) $v['doc_group_id'];
                    $loc = stripos($v['doc_content'], $_post['search_string']);
                    $beg = 0;
                    if ($loc > 50) {
                        $beg = ($loc - 50);
                    }
                    $snip      = jrCore_hilight_string(substr(strip_tags($v['doc_content']), $beg, 200), $_post['search_string']);
                    $_id[$gid] = $snip;
                }
                $_rt = array(
                    'search'        => array(
                        "_item_id in " . implode(',', array_keys($_id)),
                        "doc_category_url = {$_post['_1']}"
                    ),
                    'order_by'      => array(
                        'doc_order' => 'numerical_asc'
                    ),
                    'limit'         => 1000,
                    'skip_triggers' => true
                );
                $_rt = jrCore_db_search_items('jrDocs', $_rt);
                if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                    $_rt['_items'] = jrDocs_get_docs_visible_to_viewer($_rt['_items'], $_profile['_profile_id']);
                    foreach ($_rt['_items'] as $k => $v) {
                        $_rt['_items'][$k]['doc_snippet'] = (isset($_id["{$v['doc_group_id']}"])) ? $_id["{$v['doc_group_id']}"] : '';
                    }
                    $_profile                        = $_profile + $_rt;
                    $_profile['category']            = $_rt['_items'][0]['doc_category'];
                    $_profile['category_url']        = $_rt['_items'][0]['doc_category_url'];
                    $_profile['search_string_value'] = htmlentities(strip_tags($_post['search_string']));
                    $_profile['breadcrumb_url']      = '/search_string=' . $_profile['search_string_value'];
                    $_profile['found_documents']     = $_rt['info']['total_items'];
                    $out                             = jrCore_parse_template('item_category_index.tpl', $_profile, 'jrDocs');
                    unset($_rt);

                }
                else {
                    // No results found
                    $_profile['category']            = $_post['_1'];
                    $_profile['category_url']        = jrCore_url_string($_post['_1']);
                    $_profile['search_string_value'] = htmlentities(strip_tags($_post['search_string']));
                    $_profile['found_documents']     = 0;
                    $out                             = jrCore_parse_template('item_category_index.tpl', $_profile, 'jrDocs');
                    unset($_rt);
                }
            }
            else {

                // No results found
                $_profile['category']            = $_post['_1'];
                $_profile['category_url']        = jrCore_url_string($_post['_1']);
                $_profile['search_string_value'] = htmlentities(strip_tags($_post['search_string']));
                $_profile['found_documents']     = 0;
                $out                             = jrCore_parse_template('item_category_index.tpl', $_profile, 'jrDocs');
                unset($_rt);
            }
        }
        else {
            $_sp = array(
                'search'        => array(
                    "_profile_id = {$_profile['_profile_id']}",
                    "doc_category_url = {$_post['_1']}"
                ),
                'order_by'      => array(
                    'doc_order' => 'numerical_asc'
                ),
                'limit'         => 1000,
                'skip_triggers' => true
            );
            $_rt = jrCore_db_search_items('jrDocs', $_sp);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
                $_rt['_items'] = jrDocs_get_docs_visible_to_viewer($_rt['_items'], $_profile['_profile_id']);
                if (count($_rt['_items']) > 0) {
                    $_profile                 = $_profile + $_rt;
                    $_profile['category_url'] = $_rt['_items'][0]['doc_category_url'];
                    $_profile['category']     = $_rt['_items'][0]['doc_category'];
                }
                $_profile['breadcrumb_url'] = '';
                $out                        = jrCore_parse_template('item_category_index.tpl', $_profile, 'jrDocs');
                unset($_rt);
            }
            else {
                jrCore_page_not_found();
            }
        }
    }
    else {
        jrCore_page_not_found();
    }
    return $out;
}

//------------------------------
// profile_contents  Table Of Contents
//------------------------------
function profile_view_jrDocs_contents($_profile, $_post, $_user, $_conf)
{
    // See if we were given a search string
    if (isset($_post['search_string']) && strlen($_post['search_string']) > 0) {

        $out = false;
        $_sp = array(
            'search'        => array(
                "_profile_id = {$_profile['_profile_id']}",
                "doc_content like %{$_post['search_string']}%"
            ),
            'group_by'      => 'doc_group_id',
            'limit'         => 50,
            'skip_triggers' => true,
            'return_keys'   => array('doc_group_id', 'doc_content')
        );
        $_rt = jrCore_db_search_items('jrDocs', $_sp);
        if ($_rt && is_array($_rt) && isset($_rt['_items']) && count($_rt['_items']) > 0) {
            $_id = array();
            foreach ($_rt['_items'] as $v) {
                $gid = (int) $v['doc_group_id'];
                $loc = stripos($v['doc_content'], $_post['search_string']);
                $beg = 0;
                if ($loc > 50) {
                    $beg = ($loc - 50);
                }
                $snip      = jrCore_hilight_string(substr(jrCore_strip_html(smarty_modifier_jrCore_format_string($v['doc_content'], 0), null), $beg, 200), $_post['search_string']);
                $_id[$gid] = $snip;
            }
            $_sp = array(
                'search'        => array(
                    "_item_id in " . implode(',', array_keys($_id))
                ),
                'order_by'      => array(
                    'doc_order' => 'numerical_asc'
                ),
                'limit'         => 1000,
                'skip_triggers' => true
            );

            $_rt = jrCore_db_search_items('jrDocs', $_sp);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                $_rt['_items'] = jrDocs_get_docs_visible_to_viewer($_rt['_items'], $_profile['_profile_id']);
                if (count($_rt['_items']) > 0) {
                    foreach ($_rt['_items'] as $k => $v) {
                        $_rt['_items'][$k]['doc_snippet'] = (isset($_id["{$v['doc_group_id']}"])) ? $_id["{$v['doc_group_id']}"] : '';
                    }
                }
                $_profile                        = $_profile + $_rt;
                $_profile['category']            = "Table of Contents";
                $_profile['category_url']        = 'contents';
                $_profile['search_string_value'] = htmlentities(strip_tags($_post['search_string']));
                $_profile['breadcrumb_url']      = '/search_string=' . $_profile['search_string_value'];
                $_profile['found_documents']     = $_rt['info']['total_items'];
                $out                             = jrCore_parse_template('item_category_index.tpl', $_profile, 'jrDocs');
                unset($_rt);

            }

        }
        if (!$out) {
            // No results found
            $_profile['category']            = "Table of Contents";
            $_profile['category_url']        = 'contents';
            $_profile['search_string_value'] = htmlentities(strip_tags($_post['search_string']));
            $_profile['found_documents']     = 0;
            $out                             = jrCore_parse_template('item_category_index.tpl', $_profile, 'jrDocs');
            unset($_rt);
        }
        return $out;

    }
    else {

        $_ln = jrUser_load_lang_strings();
        jrCore_page_title("{$_ln['jrDocs'][53]} - {$_ln['jrDocs'][54]} - {$_profile['profile_name']}", false);
        $key = md5($_post['_uri']);
        if (!$out = jrCore_is_cached('jrDocs', $key)) {

            $_chapters = array();
            // Table of Contents
            $_sp = array(
                'search'                       => array(
                    "_profile_id = {$_profile['_profile_id']}",
                    "doc_section_type = header"
                ),
                'order_by'                     => array(
                    'doc_order' => 'numerical_asc'
                ),
                'exclude_jrUser_keys'          => true,
                'exclude_jrProfile_quota_keys' => true,
                'limit'                        => 5000
            );

            $_rt = jrCore_db_search_items('jrDocs', $_sp);
            if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

                $_rt['_items'] = jrDocs_get_docs_visible_to_viewer($_rt['_items'], $_profile['_profile_id']);
                if (count($_rt['_items']) > 0) {

                    $ord = 0;
                    foreach ($_rt['_items'] as $k => $item) {
                        // Only document leaders
                        if ($item['doc_group_id'] != $item['_item_id']) {
                            continue;
                        }
                        // Figure out the order in the category
                        $c_order                   = (isset($item['chapter_order']) && $item['chapter_order'] > 0) ? (int) $item['chapter_order'] : 9999;
                        $_chapters[$c_order][$ord] = $item;
                        $ord++;
                    }
                }
            }
            ksort($_chapters);
            $_rep           = $_profile;
            $_rep['_items'] = $_chapters;
            $out            = jrCore_parse_template('index_toc.tpl', $_rep, 'jrDocs');
            jrCore_add_to_cache('jrDocs', $key, $out);
        }
        return $out;
    }
}
