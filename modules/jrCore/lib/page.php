<?php
/**
 * Jamroom System Core module
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
 * @package Page Elements
 * @copyright 2012 Talldude Networks, LLC.
 * @author Brian Johnson <brian [at] jamroom [dot] net>
 */

// make sure we are not being called directly
defined('APP_DIR') or exit();

/**
 * Shows a notice message and is used when we are NOT sure if the
 * UI is even working.  Will show a plain text error message
 * @param string $type Error level (CRI, MAJ, etc.)
 * @param string $message Error Message
 */
function jrCore_notice($type, $message)
{
    jrCore_db_close();
    if (jrCore_is_ajax_request()) {
        $_out = array('notices' => array());
        $_out['notices'][] = array('type' => strtolower($type), 'text' => $message);
        echo json_encode($_out);
        exit;
    }
    $_rp = array(
        'type'    => $type,
        'message' => $message
    );
    echo jrCore_parse_template('error_notice.tpl', $_rp, 'jrCore');
    exit;
}

/**
 * Show a success/warning/error notice page and Exit
 * @param string $notice_type Notice Type (success/warning/notice/error)
 * @param string $notice_text Notice Text
 * @param string $cancel_url URL to link to Cancel Button
 * @param string $cancel_text Text for Cancel Button
 * @param bool $clean_output If true, Notice Text is run through htmlspecialchars()
 * @param bool $include_header If true, header/footer is included in output
 * @return null
 */
function jrCore_notice_page($notice_type, $notice_text, $cancel_url = null, $cancel_text = null, $clean_output = true, $include_header = true)
{
    global $_post;
    if (isset($notice_text) && jrCore_checktype($notice_text, 'number_nz')) {
        $_lang = jrUser_load_lang_strings();
        if (isset($_lang["{$_post['module']}"][$notice_text])) {
            $notice_text = $_lang["{$_post['module']}"][$notice_text];
        }
    }

    if (jrCore_is_ajax_request()) {
        $_er = array($notice_type => $notice_text);
        jrCore_json_response($_er);
        jrCore_db_close();
        exit;
    }

    jrCore_page_title($notice_type);
    jrCore_page_notice($notice_type, $notice_text, $clean_output);

    if (!$include_header) {
        jrCore_page_set_no_header_or_footer();
    }
    if (!empty($cancel_url)) {
        jrCore_page_cancel_button($cancel_url, $cancel_text);
    }

    $out = jrCore_page_display(true);
    $out = jrCore_trigger_event('jrCore', 'view_results', $out);
    jrCore_process_exit_delete_profile_cache();
    jrCore_send_response_and_detach($out);
    jrCore_trigger_event('jrCore', 'process_exit', $_REQUEST);
    jrCore_db_close();
    exit;
}

/**
 * Add a page element to the jrcore_page_elements flag for processing at view time.
 * @param string $section section string section to add to: "meta", "javascript", "css", "page", "footer"
 * @param array $_params Element information as an array to pass to the element's template
 * @return bool
 */
function jrCore_create_page_element($section, $_params)
{
    global $_post, $_mods;
    $_tmp = jrCore_get_flag('jrcore_page_elements');
    if (!$_tmp || !is_array($_tmp)) {
        $_tmp = array();
    }
    switch ($section) {
        case 'meta':
            // Meta data is simple key value pairs.
            if (!isset($_tmp[$section])) {
                $_tmp[$section] = array();
            }
            foreach ($_params as $key => $value) {
                $_tmp[$section][$key] = $value;
            }
            break;
        case 'css_embed':
        case 'javascript_embed':
        case 'javascript_ready_function':
        case 'javascript_footer_function':
            if (!isset($_tmp[$section])) {
                $_tmp[$section] = '';
            }
            foreach ($_params as $entry) {
                // See if we are including lightbox
                if (strpos($entry, 'lightbox')) {
                    jrCore_set_flag('jrcore_lightbox_included', 1);
                }
                $_tmp[$section] .= "{$entry}\n";
            }
            break;
        case 'javascript_footer_href':
        case 'javascript_href':
        case 'css_footer_href':
        case 'css_href':
            if (!isset($_tmp[$section])) {
                $_tmp[$section] = array();
            }
            foreach ($_params as $k => $prm) {
                if ($k === 'source' && !strpos($prm, '?')) {
                    if (isset($_post['module'])) {
                        if (isset($_mods["{$_post['module']}"]['module_version'])) {
                            $_params[$k] = "{$prm}?v={$_mods["{$_post['module']}"]['module_version']}";
                        }
                        else {
                            $_params[$k] = $prm;
                        }
                    }
                }
            }
            $_tmp[$section][] = $_params;
            break;

        // BELOW USED INTERNALLY - do not use in view controllers.
        case 'form_begin':
            // Only one form per page
            $_tmp['form_begin'] = $_params['form_html'];
            // We also add in to regular page elements so the item templates
            // will be rendered in the correct place
            $_tmp['page'][] = $_params;
            break;
        case 'form_end':
            $_tmp[$section] = $_params['form_html'];
            break;
        case 'form_hidden':
            if (!isset($_tmp[$section])) {
                $_tmp[$section] = array();
            }
            $_tmp[$section][] = $_params['form_html'];
            break;
        case 'form_modal':
            $_tmp['form_modal'] = $_params;
            break;

        // "page" is default
        default:
            if (!isset($_params['type']{0})) {
                jrCore_logger('CRI', "jrCore_create_page_element: required element type not received - verify usage");
                return false;
            }
            if (!isset($_tmp[$section])) {
                $_tmp[$section] = array();
            }
            $_tmp[$section][] = $_params;
            break;
    }
    jrCore_set_flag('jrcore_page_elements', $_tmp);
    return true;
}

/**
 * Convert applicable strings to HTML entities
 * @param $string string String to convert
 * @return string
 */
function jrCore_entity_string($string)
{
    return (is_string($string)) ? htmlentities($string, ENT_QUOTES, 'UTF-8', false) : $string;
}

/**
 * Highlight a string by adding the page_search_highlight class to a surrounding span
 * @param string $string String to be hilighted
 * @param string $search Sub-String within String to be hilighted
 * @return string
 */
function jrCore_hilight_string($string, $search)
{
    // Does our string have HTML in it?
    if (strpos($string, '>')) {
        // We have HTML in our string - we only highlight sections of text that are NOT inside HTML tag quotes
        // <b>Highlight in this string is OK</b><a title="But not in this one">And here is good too</a>
        preg_match_all('#<[^>]+>(.+?)#ims', $string, $_matches);
        if ($_matches && is_array($_matches[0])) {
            $temp = jrCore_entity_string(strip_tags($search));
            $_tmp = array_flip($_matches[0]);
            foreach ($_tmp as $html => $key) {
                $string = str_replace($html, "%%${key}%%", $string);
            }
            $string = str_ireplace($search, '<span class="page_search_highlight">' . $temp . '</span>', $string);
            foreach ($_tmp as $html => $key) {
                $string = str_replace("%%${key}%%", $html, $string);
            }
            return $string;
        }
    }
    return str_ireplace($search, '<span class="page_search_highlight">' . jrCore_entity_string(strip_tags($search)) . '</span>', $string);
}

/**
 * set HTML page title
 * @param string $title Title of page
 * @param bool $overwrite set to false to prevent later calls overwriting earlier
 * @return bool
 */
function jrCore_page_title($title, $overwrite = true)
{
    $title = html_entity_decode(jrCore_strip_html($title), ENT_QUOTES);
    if (!$overwrite) {
        if (!jrCore_get_flag('jrcore_html_page_title_ow')) {
            jrCore_set_flag('jrcore_html_page_title_ow', 1);
            jrCore_set_flag('jrcore_html_page_title', $title);
        }
    }
    elseif (!jrCore_get_flag('jrcore_html_page_title_ow')) {
        jrCore_set_flag('jrcore_html_page_title', $title);
    }
    return true;
}

/**
 * a page jumper select used in a form page banner
 * @param string $module Module Name
 * @param string $field Field to link to ID
 * @param array $search Search parameters for jrCore_db_search_items
 * @param string $create Create View for module
 * @param string $update Update View for module
 * @return string
 */
function jrCore_page_banner_item_jumper($module, $field, $search, $create, $update)
{
    global $_conf, $_post;
    if (!isset($search) || !is_array($search)) {
        return false;
    }
    $_sc = array(
        'search'         => $search,
        'order_by'       => array(
            $field => 'asc'
        ),
        'limit'          => 250,
        'skip_triggers'  => true,
        'privacy_check'  => false,
        'ignore_pending' => true
    );
    $c_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$create}";
    $u_url = "{$_conf['jrCore_base_url']}/{$_post['module_url']}/{$update}/id=";
    $htm = '<select name="item_id" class="form_select form_select_item_jumper" onchange="var iid=this.options[this.selectedIndex].value;if(iid == \'create\'){self.location=\'' . $c_url . '\'} else {self.location=\'' . $u_url . '\'+ iid}">' . "\n";
    if (isset($create) && strlen($create) > 0) {
        $_lang = jrUser_load_lang_strings();
        $htm .= '<option value="create"> ' . $_lang['jrCore'][50] . '</option>' . "\n";
    }
    $_rt = jrCore_db_search_items($module, $_sc);
    if (isset($_rt) && isset($_rt['_items']) && is_array($_rt['_items']) && count($_rt['_items']) > 0) {
        $_opts = array();
        foreach ($_rt['_items'] as $_v) {
            if ($module == 'jrProfile') {
                $_v['_item_id'] = $_v['_profile_id'];
            }
            $_opts["{$_v['_item_id']}"] = $_v[$field];
        }
        foreach ($_opts as $item_id => $display) {
            if (isset($_post['id']) && $item_id == $_post['id']) {
                $htm .= '<option value="' . $item_id . '" selected="selected"> ' . $display . '</option>' . "\n";
            }
            else {
                $htm .= '<option value="' . $item_id . '"> ' . $display . '</option>' . "\n";
            }
        }
        unset($_rt, $_opts);
    }
    else {
        return '';
    }
    $htm .= '</select>';
    return $htm;
}

/**
 * Show a "banner" at the top of a form page
 * @param string $title Title of section
 * @param string $subtitle Subtitle text for section
 * @param string $icon Icon image
 * @return bool
 */
function jrCore_page_banner($title, $subtitle = null, $icon = null)
{
    global $_conf, $_post;
    if (is_null($icon)) {
        $icon = false;
        $_tmp = jrCore_get_registered_module_features('jrCore', 'module_icons');
        if (isset($_tmp["{$_conf['jrCore_active_skin']}"])) {
            if ($_tmp["{$_conf['jrCore_active_skin']}"]['show'] == '1') {
                $icon = "{$_conf['jrCore_base_url']}/modules/{$_post['module']}/icon.png";
            }
            elseif ($_tmp["{$_conf['jrCore_active_skin']}"]['show'] == 'custom') {
                $icon = "{$_conf['jrCore_base_url']}/skins/{$_conf['jrCore_active_skin']}/img/{$_post['module']}_icon.png";
            }
        }
    }
    if (jrCore_checktype($title, 'number_nz') || jrCore_checktype($subtitle, 'number_nz')) {
        $_lang = jrUser_load_lang_strings();
        if (isset($_lang["{$_post['module']}"][$title])) {
            $title = $_lang["{$_post['module']}"][$title];
        }
        if (isset($_lang["{$_post['module']}"][$subtitle])) {
            $subtitle = $_lang["{$_post['module']}"][$subtitle];
        }
    }
    // If this is a Master Admin, they can customize the form they are viewing
    // If it has been registered as a Form Designer form
    if (jrUser_is_master()) {
        $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form');
        if ($_tmp && is_array($_tmp) && isset($_tmp["{$_post['module']}"]["{$_post['option']}"])) {
            $subtitle .= jrCore_page_button('fd', 'form designer', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/form_designer/m={$_post['module']}/v={$_post['option']}')");
        }
    }

    $_tmp = array(
        'type'     => 'page_banner',
        'title'    => $title,
        'subtitle' => $subtitle,
        'icon_url' => $icon,
        'module'   => 'jrCore',
        'template' => 'page_banner.tpl'
    );
    jrCore_create_page_element('page', $_tmp);

    // Set our page title too
    jrCore_page_title($title);
    return true;
}

/**
 * jrCore_page_section_header - page divider/section
 *
 * @param string $title Title of section
 * @return bool
 */
function jrCore_page_section_header($title)
{
    $_tmp = array(
        'type'     => 'page_section_header',
        'title'    => $title,
        'module'   => 'jrCore',
        'template' => 'page_section_header.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_notice - show a success/warning/error notice on a page
 *
 * @param string $notice_type Notice Type (success/warning/notice/error)
 * @param string $notice_text Notice Text
 * @param bool $clean_output If true, Notice Text is run through htmlspecialchars()
 * @return bool
 */
function jrCore_page_notice($notice_type, $notice_text, $clean_output = true)
{
    $_lang = jrUser_load_lang_strings();
    if ($clean_output) {
        $notice_text = nl2br(htmlspecialchars($notice_text));
    }
    // Get our lang string
    switch ($notice_type) {
        case 'notice':
            $string = (isset($_lang['jrCore'][22])) ? $_lang['jrCore'][22] : 'notice';
            break;
        case 'warning':
            $string = (isset($_lang['jrCore'][23])) ? $_lang['jrCore'][23] : 'warning';
            break;
        case 'error':
            $string = (isset($_lang['jrCore'][24])) ? $_lang['jrCore'][24] : 'error';
            break;
        case 'success':
            $string = (isset($_lang['jrCore'][25])) ? $_lang['jrCore'][25] : 'success';
            break;
        default:
            $string = $notice_type;
            break;
    }
    $_tmp = array(
        'type'         => 'page_notice',
        'notice_type'  => $notice_type,
        'notice_label' => $string,
        'notice_text'  => $notice_text,
        'module'       => 'jrCore',
        'template'     => 'page_notice.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_link_cell
 * Creates an entry in a form with label and URL in body
 *
 * @param string $label Text for label title
 * @param string $url URL to link to
 * @param string $sublabel Sub label for label
 * @return bool
 */
function jrCore_page_link_cell($label, $url, $sublabel = null)
{
    global $_post;
    $mod = $_post['module'];
    $_ln = jrUser_load_lang_strings();
    if (jrCore_checktype($label, 'number_nz') && isset($_ln[$mod][$label])) {
        $label = $_ln[$mod][$label];
    }
    if (jrCore_checktype($sublabel, 'number_nz') && isset($_ln[$mod][$sublabel])) {
        $sublabel = $_ln[$mod][$sublabel];
    }
    $_tmp = array(
        'type'     => 'page_link_cell',
        'label'    => $label,
        'sublabel' => (is_null($sublabel)) ? false : $sublabel,
        'url'      => $url,
        'module'   => 'jrCore',
        'template' => 'page_link_cell.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_custom - embed html into a page
 * @param string $html Text to embed into page
 * @param string $label Label for Custom HTML
 * @param string $sublabel Label for Custom HTML
 * @param string $help Help drop down for Custom HTML
 * @return bool
 */
function jrCore_page_custom($html, $label = null, $sublabel = null, $help = null)
{
    global $_post;
    $_ln = jrUser_load_lang_strings();
    $_tm = array(
        'type'     => 'page_custom',
        'name'     => substr(md5(microtime()), 8, 8),
        'html'     => $html,
        'label'    => (isset($_ln["{$_post['module']}"][$label])) ? $_ln["{$_post['module']}"][$label] : $label,
        'sublabel' => (isset($_ln["{$_post['module']}"][$sublabel])) ? $_ln["{$_post['module']}"][$sublabel] : $sublabel,
        'help'     => (isset($_ln["{$_post['module']}"][$help])) ? $_ln["{$_post['module']}"][$help] : $help,
        'module'   => 'jrCore',
        'template' => 'page_custom.tpl'
    );
    jrCore_create_page_element('page', $_tm);
    return true;
}

/**
 * embeds RAW HTML into the page (no enclosure)
 * @param string $html Text to embed into page
 * @return bool
 */
function jrCore_page_html($html)
{
    $_tmp = array(
        'type'   => 'page_html',
        'html'   => $html,
        'module' => 'jrCore'
    );
    // no template needed for this
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_divider - create a section divider on a page
 * @return bool
 */
function jrCore_page_divider()
{
    $_tmp = array(
        'type'     => 'page_divider',
        'module'   => 'jrCore',
        'template' => 'page_divider.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * Add a vertical space to a page
 * @return bool
 */
function jrCore_page_spacer()
{
    $_tmp = array(
        'type'     => 'page_spacer',
        'module'   => 'jrCore',
        'template' => 'page_spacer.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_note
 * @param string $html HTML to show
 * @param string $class CSS Class for background
 *
 * @return bool
 */
function jrCore_page_note($html, $class = 'notice')
{
    $_tmp = array(
        'type'     => 'page_note',
        'html'     => $html,
        'class'    => $class,
        'module'   => 'jrCore',
        'template' => 'page_note.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_template
 * @param string $template Template to embed in page (located in Module/templates)
 * @return bool
 */
function jrCore_page_template($template)
{
    $_tmp = array(
        'type'     => 'page_template',
        'file'     => $template,
        'module'   => 'jrCore',
        'template' => 'page_template.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_tab_bar
 *
 * @param array $_tabs Array of tabs to create on page
 *
 * @return bool
 */
function jrCore_page_tab_bar($_tabs)
{
    if (isset($_tabs) && is_array($_tabs) && count($_tabs) > 0) {
        $tab_n = count($_tabs);
        $width = round(100 / $tab_n);
        $i = 1;
        foreach ($_tabs as $k => $_cell) {
            $_tabs[$k]['id'] = 't' . jrCore_url_string($k);
            $_tabs[$k]['width'] = $width;
            $add = '';
            if (isset($_cell['class'])) {
                $add = " {$_cell['class']}";
            }
            $_tabs[$k]['class'] = 'page_tab';
            // Check for positioning
            if ($i == 1) {
                $_tabs[$k]['class'] .= ' page_tab_first';
            }
            elseif ($i == $tab_n) {
                $_tabs[$k]['class'] .= ' page_tab_last';
            }
            $_tabs[$k]['class'] .= $add;
            $i++;
        }
    }
    $_tmp = array(
        'type'     => 'page_tab_bar',
        'tabs'     => $_tabs,
        'module'   => 'jrCore',
        'template' => 'page_tab_bar.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_search
 * @param string $label Element Label
 * @param string $action Action URL for search form
 * @param string $value default value for search field
 * @param bool $show_help Show Help button true/false
 * @return bool
 */
function jrCore_page_search($label, $action, $value = null, $show_help = true)
{
    $_lng = jrUser_load_lang_strings();
    $sbtn = (isset($_lng['jrCore'][8])) ? $_lng['jrCore'][8] : 'search';
    $rbtn = (isset($_lng['jrCore'][29])) ? $_lng['jrCore'][29] : 'reset';
    $_str = array('search_string', 'p');
    $html = '<input type="text" name="search_string" id="sstr" class="form_text form_text_search" value="' . $value . '" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) {var s=$(\'#sstr\').val();jrCore_window_location(\'' . $action . '?search_string=\'+ jrE(s));return false}"><input type="button" value="' . jrCore_str_to_lower($sbtn) . '" class="form_button" onclick="var s=$(\'#sstr\').val();jrCore_window_location(\'' . $action . '?search_string=\'+ jrE(s));return false"><input type="button" value="' . jrCore_str_to_lower($rbtn) . '" class="form_button" onclick="jrCore_window_location(\'' . jrCore_strip_url_params($action, $_str) . '\')">';
    $_tmp = array(
        'type'      => 'page_search',
        'html'      => $html,
        'label'     => $label,
        'action'    => $action,
        'show_help' => ($show_help !== false) ? 1 : 0,
        'value'     => (is_null($value) || $value === false) ? false : $value,
        'module'    => 'jrCore',
        'template'  => 'page_search.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_tool_entry
 * @param string $url Tool URL
 * @param string $label Page element Label
 * @param string $description Page element Description
 * @param string $onclick Javascript onclick content
 * @param string $target Browser anchor target
 * @return bool
 */
function jrCore_page_tool_entry($url, $label, $description, $onclick = null, $target = '_self')
{
    $_tmp = array(
        'type'        => 'page_tool_entry',
        'label'       => $label,
        'label_url'   => $url,
        'description' => $description,
        'onclick'     => (is_null($onclick) || $onclick === false) ? false : $onclick,
        'target'      => $target,
        'module'      => 'jrCore',
        'template'    => 'page_tool_entry.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_table_header
 * @param array $_cells Array containing header row cells
 * @param string $class CSS Class for row
 * @param bool $inline set to TRUE if doing a "header" row inside an open table
 * @return bool
 */
function jrCore_page_table_header($_cells, $class = null, $inline = false)
{
    if (!$inline) {
        jrCore_delete_flag('jr_html_page_table_row_num');
        jrCore_delete_flag('jr_html_page_table_header_colspan');
    }
    $cls = '';
    if (!is_null($class) && strlen($class) > 0) {
        $cls = " {$class}";
    }
    $_tmp = array(
        'type'     => 'page_table_header',
        'cells'    => $_cells,
        'class'    => $cls,
        'module'   => 'jrCore',
        'inline'   => $inline,
        'template' => 'page_table_header.tpl'
    );
    $uniq = jrCore_get_flag('jr_html_page_table_header_colspan');
    if (!$uniq) {
        $uniq = count($_cells);
        jrCore_set_flag('jr_html_page_table_header_colspan', $uniq);
    }
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_table_row
 * @param array $_cells Array containing row cells
 * @param string $class CSS Class for row
 * @return bool
 */
function jrCore_page_table_row($_cells, $class = null)
{
    $rownum = jrCore_get_flag('jr_html_page_table_row_num');
    if (!$rownum) {
        $rownum = 0;
    }
    $colspan = jrCore_get_flag('jr_html_page_table_header_colspan');
    $col_cnt = count($_cells);
    ksort($_cells, SORT_NUMERIC);
    if ($colspan > $col_cnt) {
        // Adjust our last row in our cells to span the entire width
        $_tmp = array_pop($_cells);
        $_tmp['colspan'] = ' colspan="' . $colspan . '"';
        if ($col_cnt == 1) {
            $_cells = array($_tmp);
        }
        else {
            $_cells[] = $_tmp;
        }
    }
    foreach ($_cells as $k => $v) {
        if (!isset($v['colspan'])) {
            $_cells[$k]['colspan'] = '';
        }
    }
    $cls = '';
    if (!is_null($class) && strlen($class) > 0) {
        $cls = " {$class}";
    }
    $_tmp = array(
        'type'     => 'page_table_row',
        'cells'    => $_cells,
        'cellnum'  => $col_cnt,
        'class'    => $cls,
        'rownum'   => ++$rownum,
        'module'   => 'jrCore',
        'template' => 'page_table_row.tpl'
    );
    jrCore_set_flag('jr_html_page_table_row_num', $rownum);
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * Create pager for page table
 * @param array $_page page array page elements for pager including:
 *        'prev_page_num' => previous page number
 *        'this_page_num' => current page number
 *        'next_page_num' => next page number
 *        'total_pages'   => total number of pages without LIMIT clause
 * @param array $_xtra Array containing information about current data set (as returned from dbPagedQuery())
 * @return bool
 */
function jrCore_page_table_pager($_page, $_xtra = null)
{
    // We have to strip the page number (p) as well as any
    // other _xtra args we get so we don't duplicate them
    if (!is_null($_xtra) && is_array($_xtra)) {
        $_strip = $_xtra;
        $_strip['p'] = 1;
    }
    else {
        $_strip = array('p' => 1);
    }
    $this_page_url = jrCore_strip_url_params(jrCore_get_current_url(), array_keys($_strip));

    // If we have $_xtra, it means we need to add additional url vars
    if (is_array($_xtra)) {
        foreach ($_xtra as $k => $v) {
            $this_page_url .= "/{$k}=" . urlencode($v);
        }
    }
    // We always show the pager
    if (is_array($_page) && isset($_page['info']) && (isset($_page['info']['simplepagebreak']) || (isset($_page['info']['total_pages']) && jrCore_checktype(intval($_page['info']['total_pages']), 'number_nz')))) {

        $prev_page_url = '';
        if (jrCore_checktype($_page['info']['prev_page'], 'number_nz')) {
            $prev_page_url = "{$this_page_url}/p={$_page['info']['prev_page']}";
        }
        $next_page_url = '';
        if (jrCore_checktype($_page['info']['next_page'], 'number_nz')) {
            $next_page_url = "{$this_page_url}/p={$_page['info']['next_page']}";
        }

        if (isset($_page['info']['total_pages']) && $_page['info']['total_pages'] > 0) {
            $page_jumper = '<select name="p" class="form_select page-table-jumper page-table-jumper-page" onchange="jrCore_window_location(\'' . $this_page_url . '/p=\'+ $(this).val());">' . "\n";
            $i           = 1;

            // If we have A LOT OF pages, we don't want to show them all
            // otherwise it gets all bogged down sending the HTML.
            $end = ($_page['info']['total_pages'] - 100);
            $mdl = 0;
            if ($_page['info']['this_page'] > 50) {
                $mdl = ($_page['info']['this_page'] - 50);
            }
            $mdh = ($_page['info']['this_page'] + 50);
            while ($i <= $_page['info']['total_pages']) {
                if ($i == $_page['info']['this_page']) {
                    $page_jumper .= '<option value="' . $i . '" selected="selected"> ' . $i . '</option>' . "\n";
                }
                elseif ($i < 100 || $i > $end || ($i > $mdl && $i < $mdh)) {
                    $page_jumper .= '<option value="' . $i . '"> ' . $i . '</option>' . "\n";
                }
                $i++;
            }
            $page_jumper .= '</select>';
        }
        else {
            $page_jumper = $_page['info']['this_page'];
        }

        if (!isset($_page['disable_page_select']) || $_page['disable_page_select'] != true) {
            // Allow the user to override the default number of items per page
            $pagebreak = 12;
            if (isset($_COOKIE['jrcore_pager_rows']) && jrCore_checktype($_COOKIE['jrcore_pager_rows'], 'number_nz')) {
                $pagebreak = (int) $_COOKIE['jrcore_pager_rows'];
            }
            $page_select = '<select name="r" class="form_select page-table-jumper page-table-jumper-perpage" onchange="var r=this.options[this.selectedIndex].value; jrCore_set_pager_rows(r, function() { jrCore_window_location(\'' . $this_page_url . '\'); });">' . "\n";
            foreach (array(5, 10, 12, 15, 20, 25, 30, 40, 50, 75, 100) as $per_page) {
                if ($per_page == $pagebreak) {
                    $page_select .= '<option value="' . $per_page . '" selected="selected"> ' . $per_page . '</option>' . "\n";
                }
                else {
                    $page_select .= '<option value="' . $per_page . '"> ' . $per_page . '</option>' . "\n";
                }
            }
            $page_select .= '</select>';
        }
        else {
            $page_select = false;
        }

        $_tmp = array(
            'type'          => 'page_table_pager',
            'prev_page_url' => $prev_page_url,
            'this_page_url' => $this_page_url,
            'next_page_url' => $next_page_url,
            'prev_page_num' => $_page['info']['prev_page'],
            'this_page_num' => $_page['info']['this_page'],
            'next_page_num' => $_page['info']['next_page'],
            'total_pages'   => (isset($_page['info']['total_pages'])) ? jrCore_number_format($_page['info']['total_pages']) : '',
            'page_jumper'   => $page_jumper,
            'page_select'   => $page_select,
            'colspan'       => jrCore_get_flag('jr_html_page_table_header_colspan'),
            'module'        => 'jrCore',
            'template'      => 'page_table_pager.tpl'
        );
        jrCore_create_page_element('page', $_tmp);
    }
    jrCore_delete_flag('jr_html_page_table_header_colspan');
    return true;
}

/**
 * Create a footer row for a Page Table
 * @param null $_cells array Cells in footer
 * @param null $class string Row Class
 * @return bool
 */
function jrCore_page_table_footer($_cells = null, $class = null)
{
    $cls = '';
    if (!is_null($class) && strlen($class) > 0) {
        $cls = " {$class}";
    }
    $_tmp = array(
        'type'     => 'page_table_footer',
        'cells'    => $_cells,
        'class'    => $cls,
        'module'   => 'jrCore',
        'template' => 'page_table_footer.tpl'
    );
    $uniq = jrCore_get_flag('jr_html_page_table_footer_colspan');
    if (!$uniq) {
        $uniq = count($_cells);
        jrCore_set_flag('jr_html_page_table_footer_colspan', $uniq);
    }
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_cancel_button
 * @param string $cancel_url URL to redirect browser to when cancel button is clicked
 * @param string $cancel_text button text value for cancel button
 * @return bool
 */
function jrCore_page_cancel_button($cancel_url, $cancel_text = null)
{
    global $_post;
    switch ($cancel_url) {
        case 'referrer':
            $cancel_url = "history.back();";
            break;
        case 'modal_close':
            $cancel_url = '$.modal.close();';
            break;
        default:
            $cancel_url = "jrCore_window_location('{$cancel_url}')";
            break;
    }
    if (is_null($cancel_text) || $cancel_text === false) {
        $_lang = jrUser_load_lang_strings();
        $cancel_text = (isset($_lang['jrCore'][2])) ? $_lang['jrCore'][2] : 'cancel';
    }
    elseif (isset($cancel_text) && jrCore_checktype($cancel_text, 'number_nz')) {
        $_lang = jrUser_load_lang_strings();
        if (isset($_lang["{$_post['module']}"][$cancel_text])) {
            $cancel_text = $_lang["{$_post['module']}"][$cancel_text];
        }
    }
    $html = '<input type="button" class="form_button" value="' . jrCore_str_to_lower($cancel_text) . '" onclick="' . $cancel_url . '">';
    $_tmp = array(
        'type'     => 'page_cancel_button',
        'html'     => $html,
        'module'   => 'jrCore',
        'template' => 'page_cancel_button.tpl'
    );
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * Creata a "Close" button that closes a popup window
 * @param string $onclick Onclick function
 * @return bool
 */
function jrCore_page_close_button($onclick = 'self.close();')
{
    $_lng = jrUser_load_lang_strings();
    $cbtn = (isset($_lng['jrCore'][28])) ? $_lng['jrCore'][28] : 'close';
    $html = '<input type="button" class="form_button" value="' . jrCore_str_to_lower($cbtn) . '" onclick="' . $onclick . '">';
    $_tmp = array(
        'type'     => 'page_close_button',
        'html'     => $html,
        'module'   => 'jrCore',
        'template' => 'page_close_button.tpl'
    );
    // template not needed here
    jrCore_create_page_element('page', $_tmp);
    return true;
}

/**
 * jrCore_page_set_no_header_or_footer
 * @return bool
 */
function jrCore_page_set_no_header_or_footer()
{
    return jrCore_set_flag('jrcore_page_no_header_or_footer', true);
}

/**
 * jrCore_page_set_meta_header_only
 * @return bool
 */
function jrCore_page_set_meta_header_only()
{
    return jrCore_set_flag('jrcore_page_meta_header_only', true);
}

/**
 * jrCore_page_include_admin_menu
 * @return bool
 */
function jrCore_page_include_admin_menu()
{
    return jrCore_set_flag('jrcore_page_include_admin_menu', true);
}

/**
 * jrCore_get_module_index
 *
 * @param string $module module string module name
 * @return string
 */
function jrCore_get_module_index($module)
{
    // If our module is NOT active, show info
    if (!jrCore_module_is_active($module)) {
        return 'admin/info';
    }

    // We need to go through each module and get it's default page
    $_df = jrCore_get_registered_module_features('jrCore', 'default_admin_view');

    if (isset($_df[$module]) && is_array($_df[$module])) {
        $_tmp = array_keys($_df[$module]);
        return reset($_tmp);
    }

    if (is_file(APP_DIR . "/modules/{$module}/config.php")) {
        return 'admin/global';
    }

    $_quota = jrCore_get_registered_module_features('jrCore', 'quota_support');
    if (isset($_quota[$module]) || is_file(APP_DIR . "/modules/{$module}/quota.php")) {
        return 'admin/quota';
    }

    // Get registered tool views
    $_tool = jrCore_get_registered_module_features('jrCore', 'tool_view');
    if (isset($_tool[$module])) {
        return 'admin/tools';
    }

    if (isset($_lang[$module])) {
        return 'admin/language';
    }

    // all modules have an info panel
    return 'admin/info';
}

/**
 * Verify pending items are correct
 * @return bool
 */
function jrCore_verify_pending_items()
{
    // First - see if we have any LINKED items that need to be removed or approved
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $req = "SELECT pending_id AS pid, pending_linked_item_module AS plmod, pending_linked_item_id AS plid FROM {$tbl} WHERE pending_linked_item_id > 0";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if ($_rt && is_array($_rt)) {
        $_md = array();
        foreach ($_rt as $_p) {
            if (!isset($_md["{$_p['plmod']}"])) {
                $_md["{$_p['plmod']}"] = array();
            }
            $iid = (int) $_p['plid'];
            $_md["{$_p['plmod']}"][$iid] = $_p['pid'];
        }
        if (count($_md) > 0) {
            $_db = array();
            $num = 0;
            foreach ($_md as $mod => $_ids) {
                if (jrCore_is_datastore_module($mod)) {
                    if ($pfx = jrCore_db_get_prefix($mod)) {
                        $_dl = $_ids;
                        $_tm = jrCore_db_get_multiple_items($mod, array_keys($_ids), array('_item_id', "{$pfx}_pending"));
                        if ($_tm && is_array($_tm)) {
                            foreach ($_tm as $_i) {
                                if (isset($_i["{$pfx}_pending"]) && $_i["{$pfx}_pending"] >= 1) {
                                    // We are still pending...
                                    $iid = (int) $_i['_item_id'];
                                    unset($_dl[$iid]);
                                }
                            }
                        }
                        if (count($_dl) > 0) {
                            // We have items that are no longer pending - approve
                            $req = "SELECT pending_id AS pid, pending_module AS pmod, pending_item_id AS plid FROM {$tbl} WHERE pending_id IN(" . implode(',', $_dl) . ')';
                            $_pi = jrCore_db_query($req, 'NUMERIC');
                            if ($_pi && is_array($_pi)) {
                                $_pm = array();
                                foreach ($_pi as $_ap) {
                                    if (!isset($_pm["{$_ap['pmod']}"])) {
                                        $_pm["{$_ap['pmod']}"] = array();
                                    }
                                    if ($pxx = jrCore_db_get_prefix($_ap['pmod'])) {
                                        $_pm["{$_ap['pmod']}"]["{$_ap['plid']}"] = array("{$pxx}_pending" => 0);
                                    }
                                }
                                if (count($_pm) > 0) {
                                    foreach ($_pm as $pmod => $_up) {
                                        jrCore_db_update_multiple_items($pmod, $_up);
                                    }
                                }
                            }

                            // Cleanup
                            $req = "DELETE FROM {$tbl} WHERE pending_id IN(" . implode(',', $_dl) . ')';
                            $cnt = jrCore_db_query($req, 'COUNT');
                            if ($cnt > 0) {
                                $num += $cnt;
                                $_db[$mod] = $_dl;
                            }
                        }
                    }
                }
            }
            if ($num > 0) {
                jrCore_logger('INF', 'deleted ' . jrCore_number_format($num) . ' invalid pending item entries', $_db);
            }
        }
    }
    return true;
}

/**
 * Get the default dashboard pending tab
 * @return mixed|string
 */
function jrCore_get_dashboard_default_pending_tab()
{
    // Do we have pending items?
    $key = 'jrcore_active_pending_tab';
    if (!$mod = jrCore_get_flag($key)) {
        $tbl = jrCore_db_table_name('jrCore', 'pending');
        $req = "SELECT pending_module FROM {$tbl} WHERE pending_linked_item_id = 0 GROUP BY pending_module";
        $_pi = jrCore_db_query($req, 'pending_module', false, 'pending_module');
        if ($_pi && is_array($_pi)) {
            if (isset($_pi['jrUser'])) {
                $mod = 'jrUser';
            }
            else {
                $mod = reset($_pi);
            }
        }
        else {
            $mod = 'jrUser';
        }
        jrCore_set_flag($key, $mod);
    }
    return $mod;
}

/**
 * jrCore_page_dashboard_tabs
 * @param string $active active string active tab can be one of: global,quota,tools,language,templates,info
 * @return bool
 */
function jrCore_page_dashboard_tabs($active = 'online')
{
    // Our Tabs for the top of the dashboard view
    global $_conf;
    $_tabs = array();

    $curl = jrCore_get_module_url('jrCore');
    if (jrUser_is_master()) {
        $_tabs['acp'] = array(
            'label' => 'ACP',
            'url'   => "{$_conf['jrCore_base_url']}/{$curl}/admin/global"
        );
    }
    $_tabs['bigview'] = array(
        'label' => 'dashboard',
        'url'   => "{$_conf['jrCore_base_url']}/{$curl}/dashboard/bigview"
    );
    $_tabs['online'] = array(
        'label' => 'users online',
        'url'   => "{$_conf['jrCore_base_url']}/{$curl}/dashboard/online"
    );
    $_tabs['pending'] = array(
        'label' => 'pending',
        'url'   => "{$_conf['jrCore_base_url']}/{$curl}/dashboard/pending/m=" . jrCore_get_dashboard_default_pending_tab()
    );
    $_tabs['activity'] = array(
        'label' => 'activity log',
        'url'   => "{$_conf['jrCore_base_url']}/{$curl}/dashboard/activity"
    );
    $purl = jrCore_get_module_url('jrUser');
    $_tabs['browser'] = array(
        'label' => 'data browser',
        'url'   => "{$_conf['jrCore_base_url']}/{$purl}/dashboard/browser"
    );
    $_tabs['recycle_bin'] = array(
        'label' => 'recycle bin',
        'url'   => "{$_conf['jrCore_base_url']}/{$curl}/dashboard/recycle_bin"
    );
    $_tabs['queue_viewer'] = array(
        'label' => 'queue viewer',
        'url'   => "{$_conf['jrCore_base_url']}/{$curl}/dashboard/queue_viewer"
    );

    // Let other modules integrate with dashboard
    $_tabs = jrCore_trigger_event('jrCore', 'dashboard_tabs', $_tabs);

    $_tabs[$active]['active'] = true;
    jrCore_set_flag('jrcore_dashboard_active', 1);
    jrCore_page_tab_bar($_tabs);
    return true;
}

/**
 * Return TRUE of a module has a visible config (non-hidden)
 * @param $module string Module
 * @return bool
 */
function jrCore_module_has_visible_config($module)
{
    if (!$_tm = jrCore_get_flag('jrCore_module_has_visible_config')) {
        $tbl = jrCore_db_table_name('jrCore', 'setting');
        $req = "SELECT `module`, COUNT(`module`) AS cnt FROM {$tbl} WHERE `type` != 'hidden' GROUP BY `module`";
        $_tm = jrCore_db_query($req, 'module', false, 'cnt');
        jrCore_set_flag('jrCore_module_has_visible_config', $_tm);
    }
    if (isset($_tm[$module])) {
        return true;
    }
    return false;
}

/**
 * Module Tabs in ACP
 * @param string $module module string module name
 * @param string $active active string active tab can be one of: global,quota,tools,language,templates,info
 */
function jrCore_page_admin_tabs($module, $active = 'tools')
{
    global $_conf;
    $_lang = jrUser_load_lang_strings();

    // Get registered tool views
    $_tools = jrCore_get_registered_module_features('jrCore', 'tool_view');
    $_quota = jrCore_get_registered_module_features('jrCore', 'quota_support');

    // Our current module url
    $url = jrCore_get_module_url($module);

    // Our admin tabs for the top of the view
    $_tabs = array();
    if (jrCore_module_is_active($module) && jrCore_module_has_visible_config($module)) {
        $_tabs['global'] = array(
            'label' => 'global config',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/global"
        );
    }
    if (jrCore_module_is_active($module) && (isset($_quota[$module]) || is_file(APP_DIR . "/modules/{$module}/quota.php"))) {
        if (isset($_quota[$module]) || strpos(file_get_contents(APP_DIR . "/modules/{$module}/quota.php"), 'jrProfile_register_quota_setting')) {
            $_tabs['quota'] = array(
                'label' => 'quota config',
                'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/quota"
            );
        }
    }

    // Check for additional tabs registered by the module
    $_tmp = jrCore_get_registered_module_features('jrCore', 'admin_tab');
    $_tmp = (isset($_tmp[$module])) ? $_tmp[$module] : false;
    if (is_array($_tmp)) {
        $_tab = array();
        $murl = jrCore_get_module_url($module);
        foreach ($_tmp as $view => $label) {
            // There are some views we cannot set
            switch ($view) {
                case 'global':
                case 'quota':
                case 'tools':
                case 'language':
                case 'templates':
                case 'style':
                case 'images':
                case 'info':
                    continue;
                    break;
            }
            $_tab[$view] = array(
                'label' => $label,
                'url'   => "{$_conf['jrCore_base_url']}/{$murl}/{$view}"
            );
        }
        $_tabs = $_tabs + $_tab;
    }

    if (jrCore_module_is_active($module) && (isset($_tools[$module]) || jrCore_db_get_prefix($module))) {
        $_tabs['tools'] = array(
            'label' => 'tools',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/tools"
        );
    }
    else {
        // We can't set tools for out default here as there is no tools...
        if ($active == 'tools') {
            $active = 'info';
        }
    }
    if (jrCore_module_is_active($module) && isset($_lang[$module])) {
        $_tabs['language'] = array(
            'label' => 'language',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/language"
        );
    }
    if (jrCore_module_is_active($module) && is_dir(APP_DIR . "/modules/{$module}/img")) {
        $_fl = glob(APP_DIR . "/modules/{$module}/img/*{.png,.jpg,.gif}", GLOB_BRACE);
        if (count($_fl) > 0) {
            $_tabs['images'] = array(
                'label' => 'images',
                'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/images"
            );
        }
    }
    if (jrCore_module_is_active($module) && is_dir(APP_DIR . "/modules/{$module}/templates")) {
        $_tabs['templates'] = array(
            'label' => 'templates',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/templates"
        );
    }
    $_tabs['info'] = array(
        'label' => 'info',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/admin/info"
    );

    if (isset($_tabs[$active])) {
        $_tabs[$active]['active'] = true;
    }
    // admin_tabs event
    $_args = array(
        'active' => $active
    );
    $_tabs = jrCore_trigger_event('jrCore', 'admin_tabs', $_tabs, $_args);
    jrCore_page_tab_bar($_tabs);
}

/**
 * Create tabs for each module with pending items
 * @param string $active
 * @return bool
 */
function jrCore_dashboard_pending_tabs($active = 'jrUser')
{
    global $_mods, $_conf, $_post;
    $tbl = jrCore_db_table_name('jrCore', 'pending');
    $tb2 = jrCore_db_table_name('jrCore', 'module');
    $req = "SELECT COUNT(p.pending_id) AS cnt, p.pending_module FROM {$tbl} p LEFT JOIN {$tb2} m ON (m.module_directory = p.pending_module)
             WHERE p.pending_linked_item_id = 0 GROUP BY p.pending_module ORDER BY m.module_name ASC";
    $_rt = jrCore_db_query($req, 'NUMERIC');
    if (!$_rt || !is_array($_rt)) {
        if (isset($_post['m']) && jrCore_module_is_active($_post['m'])) {
            $_rt[] = array('pending_module' => $_post['m'], 'cnt' => 0);
        }
        else {
            $_rt[] = array('pending_module' => 'jrUser', 'cnt' => 0);
        }
    }
    $_tabs = array();
    foreach ($_rt as $v) {
        if (jrCore_module_is_active($v['pending_module'])) {
            $mod = $v['pending_module'];
            $_tabs[$mod] = array(
                'label' => $_mods["{$v['pending_module']}"]['module_name'],
                'url'   => "{$_conf['jrCore_base_url']}/{$_post['module_url']}/dashboard/pending/m={$mod}"
            );
            if ($active == $v['pending_module']) {
                $_tabs[$mod]['active'] = true;
            }
        }
    }
    if (count($_tabs) > 0) {
        jrCore_page_tab_bar($_tabs);
    }
    return true;
}

/**
 * Tabs shown for a skin in the ACP
 * @param string $skin Active Skin
 * @param string $active active string active tab can be one of: global,style,images,language,templates,info
 */
function jrCore_page_skin_tabs($skin, $active = 'info')
{
    global $_conf;
    // Core Module URL
    $url   = jrCore_get_module_url('jrCore');
    $_tabs = array();
    if (is_file(APP_DIR . "/skins/{$skin}/config.php")) {
        $_tabs['global'] = array(
            'label' => 'global config',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/skin_admin/global/skin={$skin}"
        );
    }
    $_tabs['style'] = array(
        'label' => 'style',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/skin_admin/style/skin={$skin}"
    );
    $_tabs['images'] = array(
        'label' => 'images',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/skin_admin/images/skin={$skin}"
    );
    if (is_dir(APP_DIR ."/skins/{$skin}/lang")) {
        $_tabs['language'] = array(
            'label' => 'language',
            'url'   => "{$_conf['jrCore_base_url']}/{$url}/skin_admin/language/skin={$skin}"
        );
    }
    $_tabs['templates'] = array(
        'label' => 'templates',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/skin_admin/templates/skin={$skin}"
    );
    $_tabs['info'] = array(
        'label' => 'info',
        'url'   => "{$_conf['jrCore_base_url']}/{$url}/skin_admin/info/skin={$skin}"
    );
    if (isset($_tabs[$active])) {
        $_tabs[$active]['active'] = true;
    }

    // skin_tabs event
    $_args = array(
        'active' => $active
    );
    $_tabs = jrCore_trigger_event('jrCore', 'skin_tabs', $_tabs, $_args);

    jrCore_page_tab_bar($_tabs);
}

/**
 * Add Javascript for ACP accordion menu to page
 * @param $category string default category
 * @return bool
 */
function jrCore_admin_menu_accordion_js($category)
{
    $cat = strtolower($category);
    // We want to hide ALL categories except the category we are currently working in.
    $hide = "\n" . 'var allPanels = $(\'.accordion > dd\').hide();$(\'.accordion > dd[id="c' . $cat . '"]\').show();' . "\n";
    $_js = array('(function($) { ' . $hide . '
    $(\'.accordion > a > dt\').click(function() {
    var p = $(this).parent().next();
    if (p.is(\':hidden\')) { allPanels.slideUp(); p.slideDown(); }
    return false; }); })(jQuery);');
    jrCore_create_page_element('javascript_ready_function', $_js);
    return true;
}

/**
 * Parse a set of page elements and display them
 * @param bool $return_html Set to true to return HTML instead of display
 * @return mixed
 */
function jrCore_page_display($return_html = false)
{
    global $_post, $_mods, $_conf;
    // See if have an open form on the page - if we do, close it up
    // with our submit and and bring it into the page

    // See if we are doing an ADMIN MENU VIEW for this module/view
    $admn = jrCore_get_flag('jrcore_page_include_admin_menu');
    if ($admn) {
        if (isset($_post['skin'])) {

            $_adm = array(
                'active_tab' => 'skins',
                '_skins'     => jrCore_get_acp_skins()
            );
            $_mta = jrCore_skin_meta_data($_post['skin']);
            $_adm['default_category'] = 'general';
                if (isset($_mta['category']) && strlen($_mta['category']) > 0) {
                $_adm['default_category'] = trim(strtolower($_mta['category']));
            }
        }
        else {
            $_adm = array(
                '_modules'   => array(),
                'active_tab' => 'modules'
            );

            $_tmp = array();
            $_ina = array();
            foreach ($_mods as $mod_dir => $_inf) {
                if ($_inf['module_active'] == '1') {
                    $_tmp[$mod_dir] = $_inf['module_name'];
                }
                else {
                    $_ina[$mod_dir] = $_inf['module_name'];
                }
            }
            asort($_tmp, SORT_NATURAL);
            asort($_ina, SORT_NATURAL);
            $_tmp = array_merge($_tmp, $_ina);

            $_out = array();
            foreach ($_tmp as $mod_dir => $ignored) {
                if (!isset($_mods[$mod_dir]['module_category'])) {
                    $_mods[$mod_dir]['module_category'] = 'tools';
                }
                $cat = $_mods[$mod_dir]['module_category'];
                if (!isset($_out[$cat])) {
                    $_out[$cat] = array();
                }
                $_out[$cat][$mod_dir] = $_mods[$mod_dir];
            }
            $_adm['_modules']['core'] = $_out['core'];
            unset($_out['core']);
            $_adm['_modules'] = $_adm['_modules'] + $_out;
            ksort($_adm['_modules']);
            unset($_out);

            $_adm['default_category'] = 'core';
            if (isset($_post['module']) && isset($_mods["{$_post['module']}"]) && isset($_mods["{$_post['module']}"]['module_category'])) {
                $_adm['default_category'] = $_mods["{$_post['module']}"]['module_category'];
            }
        }
        jrCore_admin_menu_accordion_js($_adm['default_category']);
    }

    // See if we have an active form session
    $_form = jrCore_form_get_session();

    // Setup module
    $module = $_form['form_params']['module'];
    $design = false;

    // Make sure we have not already displayed this form (i.e. the form is embedded into another page)
    $tmp = jrCore_get_flag("jrcore_page_display_form_{$_form['form_token']}");
    if (!$tmp && isset($_form) && is_array($_form) && isset($_form['form_params'])) {

        $_form['form_fields'] = jrCore_get_flag('jrcore_form_session_fields');

        // If our form info changes from a listener, reload
        $_tfrm = jrCore_get_flag('jrcore_form_session_fields');
        if ($_tfrm !== $_form['form_fields']) {
            $_form['form_fields'] = $_tfrm;
        }
        unset($_tfrm);

        // Check and see if this form is registered with the form designer
        if (isset($_form['form_fields']) && is_array($_form['form_fields']) && count($_form['form_fields']) > 0) {

            $_tmp = jrCore_get_registered_module_features('jrCore', 'designer_form');

            // if our install flag is set, and this form has registered for the form designer, we need to make sure we are setup.
            if (isset($_tmp["{$_post['module']}"]["{$_post['option']}"])) {

                $_fld = jrCore_get_designer_form_fields($_post['module'], $_post['option']);
                $_sfd = $_fld;  // backup

                if (jrUser_is_master()) {
                    // This is a designer form - make sure the fields are setup
                    foreach ($_form['form_fields'] as $k => $_field) {
                        if (!isset($_fld) || !is_array($_fld) || !isset($_fld["{$_field['name']}"])) {
                            $_field['active'] = 1;
                            $_field['order']  = ($k + 1);
                            jrCore_verify_designer_form_field($_post['module'], $_post['option'], $_field);
                        }
                    }
                }

                // Next - let's get all our designer info about this module/view so we can override
                // what is coming in from the actual module view
                if (is_array($_fld)) {
                    $design = true;
                    // Go through and remove the fields we have already substituted
                    foreach ($_form['form_fields'] as $_field) {
                        if (isset($_field['name']) && isset($_fld["{$_field['name']}"])) {
                            unset($_fld["{$_field['name']}"]);
                        }
                    }
                    // See if we have any NEW fields left over
                    if (is_array($_fld) && count($_fld) > 0) {
                        $_val = jrCore_get_flag('jrcore_form_create_values');
                        foreach ($_fld as $_field) {
                            if (isset($_field['active']) && $_field['active'] == '1') {
                                // If this is a meter based field, we need to pass in a copy of the active item id as "value".
                                if (!isset($_field['value'])) {
                                    if (isset($_val["{$_field['name']}_size"])) {
                                        // We have a file based field - add in value
                                        $_field['value'] = $_val;
                                    }
                                }
                                // Make sure image_delete is added in for images
                                if (isset($_field['type']) && $_field['type'] == 'image') {
                                    $_field['image_delete'] = true;
                                }
                                $_field['is_form_designer_field'] = true;
                                jrCore_form_field_create($_field, $module, null, false);
                            }
                        }
                    }
                }
            }

            // If this is the FIRST load of a form that is a designer form, $_fld will be
            // empty and $design will be false - we change that here if needed.
            if (isset($_tmp["{$_post['module']}"]["{$_post['option']}"])) {
                $design = true;
            }

            // Bring in any additional form fields added by modules
            $_form = jrCore_trigger_event('jrCore', 'form_display', $_form);

            // Make sure additional fields form listeners are added in
            $_form['form_fields'] = jrCore_get_flag('jrcore_form_session_fields');
        }

        // Bring in lang strings
        $_lang = jrUser_load_lang_strings();

        $undo = false;
        if (isset($_form['form_params']['reset'])) {
            if (isset($_form['form_params']['reset_value']) && isset($_lang[$module]["{$_form['form_params']['reset_value']}"])) {
                $undo = $_lang[$module]["{$_form['form_params']['reset_value']}"];
            }
            else {
                $undo = (isset($_lang['jrCore'][9])) ? $_lang['jrCore'][9] : 'reset';
            }
        }
        $cancel_text = false;
        $cancel_url = false;
        if (isset($_form['form_params']['cancel']{0})) {

            // Cancel text
            if (isset($_form['form_params']['cancel_value']) && isset($_lang[$module]["{$_form['form_params']['cancel_value']}"])) {
                $cancel_text = $_lang[$module]["{$_form['form_params']['cancel_value']}"];
            }
            elseif (isset($_form['form_params']['cancel_value']{0})) {
                $cancel_text = $_form['form_params']['cancel_value'];
            }
            else {
                $cancel_text = (isset($_lang['jrCore'][2])) ? $_lang['jrCore'][2] : 'cancel';
            }

            // Cancel Url
            if ($_form['form_params']['cancel'] == 'referrer') {
                $cancel_url = jrCore_get_local_referrer();
            }
            elseif ($_form['form_params']['cancel'] == 'modal_close') {
                $cancel_url = '$.modal.close();';
            }
            else {
                $cancel_url = $_form['form_params']['cancel'];
            }
        }
        // get lang replacements in place
        if (isset($_form['form_params']['submit_value'])) {
            if (isset($_lang[$module]["{$_form['form_params']['submit_value']}"])) {
                $_form['form_params']['submit_value'] = $_lang[$module]["{$_form['form_params']['submit_value']}"];
            }
        }
        else {
            $_form['form_params']['submit_value'] = '';
        }

        if (isset($_SESSION['quota_max_items_reached'])) {
            jrCore_page_cancel_button($cancel_url, $cancel_text);
        }
        else {
            jrCore_form_submit($_form['form_params']['submit_value'], $undo, $cancel_text, $cancel_url);
        }
        jrCore_form_end();

        // Lastly - save all fields that rolled out on this form to the form session
        if (isset($_form['form_fields']) && is_array($_form['form_fields'])) {
            $tbl = jrCore_db_table_name('jrCore', 'form_session');
            $tkn = jrCore_db_escape($_form['form_token']);
            $sav = jrCore_db_escape(json_encode($_form['form_fields']));
            $req = "UPDATE {$tbl} SET form_updated = UNIX_TIMESTAMP(), form_rand = '" . mt_rand() . "', form_fields = '{$sav}' WHERE form_token = '{$tkn}'";
            jrCore_db_query($req);
        }
        // We only ever show a form once per page display
        jrCore_set_flag("jrcore_page_display_form_{$_form['form_token']}", 1);
    }

    $html = '';
    $page = '';
    $_rep = array();
    $_tmp = jrCore_get_flag('jrcore_page_elements');

    // $_tmp['page'] contains all the page elements we are going to be showing on
    // this view - if we are a designer form, we need to adjust our field order here
    if ($design && isset($_tmp['page']) && is_array($_tmp['page'])) {

        // We have to do a quick pre-scan here and put any Chained Select fields into the proper order (0,1,2)
        $_cs = false;
        foreach ($_tmp['page'] as $k => $_field) {
            if (isset($_field['name']) && $_field['type'] == 'select' && isset($_field['onchange']) && strpos(' ' . $_field['onchange'], 'jrChainedSelect')) {
                $idx = (int) jrCore_string_field($_field['name'], 'NF', '_');
                $val = (isset($_field['order'])) ? (int) $_field['order'] : $k;
                if ($idx == 0) {
                    // This is our "initial" select field - 1 and 2 must come after
                    $nam = str_replace('_0', '', $_field['name']);
                    if (!$_cs) {
                        $_cs = array();
                    }
                    $_cs[$nam] = $val;
                    break;
                }
            }
        }

        $_or = array();
        $_nw = array();
        $num = 1;
        $elm = 0;
        $tab = -100;

        foreach ($_tmp['page'] as $k => $_field) {

            // Make sure field is active
            if (isset($_field['active']) && $_field['active'] == '0') {
                unset($_tmp['page'][$k]);
                continue;
            }

            // Make sure the user has permissions to view this field
            if (isset($_field['group'])) {
                if (!jrCore_user_is_part_of_group($_field['group'])) {
                    unset($_tmp['page'][$k]);
                    continue;
                }
            }

            // If this is a mobile device, and we are asking for an editor, we use a text area instead
            if ($_field['type'] == 'editor' && jrCore_is_mobile_device()) {
                $_field['type'] = 'textarea';
            }

            // We need to check here for form fields.  ALL form fields must
            // come after the opening form element - so we first must scan for our
            // opening form element and make sure it comes before the form fields.
            if (isset($_field['name'])) {
                // If this is a "no form designer field" we come last
                if (isset($_field['form_designer']) && $_field['form_designer'] === false) {
                    if (!isset($_field['order'])) {
                        $val = (8000 + $elm);
                    }
                    else {
                        $val = (intval($_field['order']) * 100);
                    }
                }
                else {
                    // If this is a CHAINED SELECT field, we need to make
                    // sure all of the options flow in the right order
                    if ($_field['type'] == 'select' && is_array($_cs)) {
                        // Find out number of select we are (0,1,2)
                        $idx = (int) jrCore_string_field($_field['name'], 'NF', '_');
                        $nam = str_replace("_{$idx}", '', $_field['name']);
                        if (isset($_cs[$nam])) {
                            if ($idx == 0) {
                                $val = $_cs[$nam];
                            }
                            else {
                                $val = floatval("{$_cs[$nam]}.{$idx}");
                            }
                        }
                        else {
                            $val = (isset($_field['order'])) ? (int) $_field['order'] : $k;
                        }
                    }
                    else {
                        if (isset($_sfd["{$_field['name']}"]['order'])) {
                            // Form Designer value (in $_sfd) is FIRST
                            $val = (int) $_sfd["{$_field['name']}"]['order'];
                        }
                        elseif (isset($_field['order'])) {
                            // Defined in controller is SECOND
                            $val = (int) $_field['order'];
                        }
                        else {
                            $val = $k;
                        }
                    }
                    $val = ($val * 100);
                }
                if (in_array($val, $_or)) {
                    $val += 25;
                }
                $_or[$num] = $val;
                $elm += 100;
            }
            else {
                switch ($_field['type']) {
                    case 'page_tab_bar':
                        // Tabs always appear at the top
                        $_or[$num] = $tab++;
                        break;
                    case 'page_banner':
                        $_or[$num] = -1;
                        break;
                    case 'form_submit':
                        $_or[$num] = 100000;
                        break;
                    case 'form_begin':
                    case 'page_notice':
                        $_or[$num] = 0;
                        break;
                    default:
                        $elm += 100;
                        $_or[$num] = $elm;
                        break;
                }
            }
            $_nw[$num] = $_field;
            $num++;
        }
        $_fn = array();
        if (isset($_nw) && is_array($_nw) && count($_nw) > 0) {
            asort($_or, SORT_NUMERIC);
            $ti = 1;
            foreach ($_or as $k => $num) {
                // Fix tab index order
                if (isset($_nw[$k]['name']) && isset($_nw[$k]['html']) && strpos($_nw[$k]['html'], 'tabindex')) {
                    $_nw[$k]['html'] = preg_replace('/tabindex="[0-9]*"/', 'tabindex="' . $ti . '"', $_nw[$k]['html']);
                    $ti++;
                }
                $_fn[] = $_nw[$k];
                unset($_nw[$k]);

            }
            $_tmp['page'] = $_fn;
        }
    }

    //--------------------------------------
    // PROCESS VIEW
    //--------------------------------------

    // Begin page output
    $meta = false;
    $temp = jrCore_get_flag('jrcore_page_no_header_or_footer');
    if (!$temp) {
        // Check for META header only
        $meta = jrCore_get_flag('jrcore_page_meta_header_only');
        if ($meta) {
            $html .= jrCore_parse_template('meta.tpl', $_tmp) . "\n<body>";
        }
        else {
            // Check for backup header elements
            $_bkp = jrCore_get_flag('jrcore_page_elements_backup');
            if ($_bkp) {
                if (isset($_bkp['javascript_ready_function'])) {
                    if (isset($_tmp['javascript_ready_function'])) {
                        $_tmp['javascript_ready_function'] .= $_bkp['javascript_ready_function'];
                    }
                    else {
                        $_tmp['javascript_ready_function'] = $_bkp['javascript_ready_function'];
                    }
                }
                $_chk = array('javascript_href', 'css_href');
                foreach ($_chk as $hitem) {
                    if (isset($_tmp[$hitem]) && isset($_bkp[$hitem])) {
                        $_tmp[$hitem] = $_tmp[$hitem] + $_bkp[$hitem];
                    }
                }
            }
            $html .= jrCore_parse_template('header.tpl', $_tmp);
        }
    }
    else {
        // With no header being shown, any form elements added in that
        // need JS/CSS added to the meta will not function properly. Save
        // these off here so they can be added in later.
        jrCore_set_flag('jrcore_page_elements_backup', $_tmp);
    }

    // We have to check for our form begin/end - they need to sit outside
    // of any tables or page elements.
    if (isset($_tmp['form_begin']{0})) {
        $page .= $_tmp['form_begin'];
    }

    // Any hidden form elements need to follow the form_begin
    if (isset($_tmp['form_hidden']) && is_array($_tmp['form_hidden'])) {
        foreach ($_tmp['form_hidden'] as $v) {
            $page .= $v;
        }
    }

    // Bring in page begin
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/page_begin.tpl")) {
        $page .= jrCore_parse_template('page_begin.tpl', $_rep);
    }
    else {
        $page .= jrCore_parse_template('page_begin.tpl', $_rep, 'jrCore');
    }

    // If we are a master admin user viewing Global or Quota config, we need to
    // set a flag so the "updated" time and default button show in help
    $show_update = '0';
    if (jrUser_is_master() && $_post['option'] == 'admin' && isset($_post['_1'])) {
        switch ($_post['_1']) {
            case 'global':
            case 'quota':
                $show_update = '1';
                break;
        }
    }

    // Parse form/page elements
    $_seen = array();
    $_sec  = array();
    foreach ($_tmp['page'] as $k => $_element) {

        // Make sure we only ever show each "name" once
        // this should never happen, but we've seen modules that
        // are not handling custom form insertions properly where
        // this could be the case
        if (isset($_element['name'])) {
            if (isset($_SESSION['quota_max_items_reached'])) {
                continue;
            }
            if (isset($_seen["{$_element['name']}"])) {
                unset($_tmp['page'][$k]);
                continue;
            }
            $_seen["{$_element['name']}"] = 1;
        }
        $_element['show_update_in_help'] = $show_update;
        if (!isset($_element['module'])) {
            jrCore_logger('CRI', "jrCore_page_display: element added without module set", $_element);
        }

        // For some element types we need to set the "default_label"
        switch ($_element['type']) {
            case 'select':
                $_element['default_label'] = (isset($_element['default']) && isset($_element['options']["{$_element['default']}"])) ? $_element['options']["{$_element['default']}"] : false;
                break;
            case 'editor':
                // If this is a mobile device, and we are asking for an editor, we use a textarea instead
                $_element['type'] = 'textarea';
                break;
            case 'page_section_header':
                // We only show section headers 1 time
                if (isset($_element['section'])) {
                    $_sec["{$_element['section']}"] = 1;
                }
                break;
        }

        // Setup our default value properly for display
        $_element['default_value'] = '';
        $_element['saved_value'] = '';
        if (isset($_element['default']) && is_string($_element['default'])) {
            $_element['default_value'] = str_replace(array("\r\n", "\r", "\n"), '\n', addslashes($_element['default']));
        }
        if (isset($_element['saved_value']) && is_string($_element['saved_value'])) {
            $_element['saved_value'] = str_replace(array("\r\n", "\r", "\n"), '\n', addslashes($_element['saved_value']));
        }

        // Check for section
        if (isset($_element['section']) && strlen($_element['section']) > 0 && !isset($_sec["{$_element['section']}"])) {
            $page .= jrCore_parse_template('page_section_header.tpl', array('title' => $_element['section']), 'jrCore');
            $_sec["{$_element['section']}"] = 1;
        }

        // Check for template - if we have a template, render it - else use HTML that comes from function
        if (isset($_element['template']{0})) {
            // Our skin can override any core/module template
            if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/{$_element['template']}")) {
                $page .= jrCore_parse_template($_element['template'], $_element);
            }
            elseif (is_file(APP_DIR . "/modules/{$_element['module']}/templates/{$_element['template']}")) {
                $page .= jrCore_parse_template($_element['template'], $_element, $_element['module']);
            }
            else {
                // default to core
                $page .= jrCore_parse_template($_element['template'], $_element, 'jrCore');
            }
        }
        elseif (isset($_element['html']{0})) {
            $page .= $_element['html'];
        }
    }
    if (isset($_SESSION['quota_max_items_reached'])) {
        unset($_SESSION['quota_max_items_reached']);
    }
    unset($_seen);

    // Bring in page end
    if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/page_end.tpl")) {
        $page .= jrCore_parse_template('page_end.tpl', $_rep);
    }
    else {
        $page .= jrCore_parse_template('page_end.tpl', $_rep, 'jrCore');
    }

    // We have to check for our form begin/end - they need to sit outside
    // of any tables or page elements.
    if (isset($_tmp['form_end'])) {
        $page .= $_tmp['form_end'];
    }

    // as well as modal window HTML
    if (isset($_tmp['form_modal'])) {
        $page .= jrCore_parse_template($_tmp['form_modal']['template'], array_merge($_rep, $_tmp['form_modal']), 'jrCore');
    }

    // See if we are doing an ADMIN MENU VIEW for this module/view
    $dash = jrCore_get_flag('jrcore_dashboard_active');
    if ($admn && isset($_adm)) {
        $_adm['admin_page_content'] = $page;
        jrCore_install_new_modules();
        $html .= jrCore_parse_template('admin.tpl', $_adm, 'jrCore');
    }
    elseif ($dash) {
        $_rep = array(
            'dashboard_html' => $page
        );
        // See if our skin is overriding our core admin template
        if (is_file(APP_DIR . "/skins/{$_conf['jrCore_active_skin']}/dashboard.tpl")) {
            $html .= jrCore_parse_template('dashboard.tpl', $_rep);
        }
        else {
            $html .= jrCore_parse_template('dashboard.tpl', $_rep, 'jrCore');
        }
    }
    else {
        $html .= $page;
    }

    // Bring in footer
    if (!$temp) {
        // Check for META header only
        if ($meta) {
            $html .= "\n</body>";
        }
        else {
            $html .= jrCore_parse_template('footer.tpl', $_tmp);
        }
    }
    else {
        // Reset for next show
        jrCore_delete_flag('jrcore_page_no_header_or_footer');
    }

    // Delete page and form elements
    $_tmp = jrCore_get_flag('jrcore_page_elements');
    if ($_tmp) {
        foreach ($_tmp as $k => $v) {
            if (strpos($k, 'javascript') !== 0 && strpos($k, 'css') !== 0) {
                unset($_tmp[$k]);
            }
        }
        jrCore_set_flag('jrcore_page_elements', $_tmp);
    }

    if ($return_html) {
        return $html;
    }
    echo $html;
    return true;
}

/**
 * The jrCore_page_buttonCode function is used for generating the necessary "button"
 * HTML code in the Jamroom Control Panel.  This ensures the Control Panel buttons
 * can be styled via the form.tpl file.
 * form.tpl element name: form_button
 *
 * @param string $name Value for Button
 * @param string $value Value for onclick handler
 * @param string $onclick If the button needs a name parameter, you can provide it here
 * @param array $_att Additional HTML <input> tag parameters
 *
 * @return string Returns HTML of button code
 */
function jrCore_page_button($name, $value, $onclick, $_att = null)
{
    // Check for provided class...
    $cls = 'form_button';
    if (isset($_att['class'])) {
        $cls = $_att['class'];
        unset($_att['class']);
    }
    $value = jrCore_entity_string(html_entity_decode($value, ENT_QUOTES));
    if (isset($onclick) && $onclick == 'disabled') {
        $html = '<input type="button" id="' . $name . '" class="' . $cls . ' form_button_disabled" name="' . $name . '" value="' . $value . '" disabled="disabled"';
    }
    else {
        $html = '<input type="button" id="' . $name . '" class="' . $cls . '" name="' . $name . '" value="' . $value . '" onclick="' . $onclick . '"';
    }
    if (isset($_att) && is_array($_att)) {
        foreach ($_att as $key => $attr) {
            $html .= ' ' . $key . '="' . $attr . '"';
        }
    }
    $html .= '>';
    return $html;
}

/**
 * jrCore_show_pending_notice
 * @param string $module Module
 * @param array $_item Item info
 * @param bool $return_output set to TRUE to return button/message output
 * @return bool
 */
function jrCore_show_pending_notice($module, $_item, $return_output = false)
{
    global $_conf;
    $prefix = jrCore_db_get_prefix($module);
    if ($module != 'jrUser' && (!isset($_item["{$prefix}_pending"]) || intval($_item["{$prefix}_pending"]) === 0)) {
        // This item is NOT pending
        return true;
    }
    if ($module == 'jrUser') {
        if (isset($_item['user_validated']) && $_item['user_validated'] == '0') {
            $_item["{$prefix}_pending"] = 1;
        }
        else {
            // User is already validated...
            return true;
        }
        $tag = 'user';
    }
    else {
        $tag = 'item';
    }

    // Are we coming from the dashboard?
    $ref = jrCore_get_local_referrer();
    if (strpos($ref, '/dashboard/')) {
        $_SESSION['user_dashboard_referral_url'] = $ref;
    }

    $pnd = (int) $_item["{$prefix}_pending"];
    // We are pending - show notice to normal users, approval options to admin users
    if (jrUser_is_admin()) {

        // Pending Status
        // 1 = Pending Approval
        // 2 = Pending Approval - Rejected
        switch ($pnd) {
            case 2:
                $out = "This {$tag} was <b>rejected</b> and is not visible to users until approved.";
                // Do we have out reason(s) saved on this one?
                if (isset($_item["{$prefix}_pending_reason"]) && strlen($_item["{$prefix}_pending_reason"]) > 0) {
                    $out .= "<br>" . nl2br($_item["{$prefix}_pending_reason"]);
                }
                $out .= '<br><br>';
                $shw = false;
                break;
            default:
                $out = "This {$tag} is currently <b>pending approval</b> and will be visible to other users once approved.<br><br>";
                $shw = true;
                break;
        }

        $url = jrCore_get_module_url('jrCore');
        if ($module == 'jrUser') {
            $srl = jrCore_get_module_url('jrUser');
            $out .= jrCore_page_button("approve", 'approve', "if (confirm('Activate this User Account and send them an email?')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$srl}/user_activate/user_id={$_item['_user_id']}') }") . '&nbsp;';
            $out .= jrCore_page_button("delete", 'delete', "if(confirm('Are you sure you want to delete this User Account? This will also deleted the User Profile associated with this account.')) { jrCore_window_location('{$_conf['jrCore_base_url']}/{$srl}/delete_save/id={$_item['_user_id']}') }");
        }
        else {
            $out .= jrCore_page_button('approve', 'approve', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_approve/{$module}/id={$_item['_item_id']}')") . '&nbsp';
            if ($shw) {
                $out .= jrCore_page_button('reject', 'reject', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_reject/{$module}/id={$_item['_item_id']}')") . '&nbsp';
            }
            $out .= jrCore_page_button('delete', 'delete', "if(confirm('Are you sure you want to delete this {$tag}? No notice will be sent.')){ jrCore_window_location('{$_conf['jrCore_base_url']}/{$url}/pending_item_delete/{$module}/id={$_item['_item_id']}')}");
        }
        if ($return_output) {
            return $out;
        }
        jrCore_page_notice('notice', $out, false);
    }
    else {

        $_lang = jrUser_load_lang_strings();
        if ($module == 'jrUser' && !jrUser_is_admin()) {
            // We already tell them if their account is pending
            return true;
        }
        // Pending Status
        // 1 = Pending Approval
        // 2 = Pending Approval - Rejected
        switch ($pnd) {
            case 2:
                if ($return_output) {
                    return $_lang['jrCore'][99];
                }
                $not = $_lang['jrCore'][99];
                // Do we have out reason(s) saved on this one?
                if (isset($_item["{$prefix}_pending_reason"]) && strlen($_item["{$prefix}_pending_reason"]) > 0) {
                    $not .= "<br>" . nl2br($_item["{$prefix}_pending_reason"]);
                }
                break;

            default:
                if ($return_output) {
                    return $_lang['jrCore'][71];
                }
                $not = $_lang['jrCore'][71];
                break;
        }
        jrCore_page_notice('error', $not, false);
    }
    return true;
}

/**
 * @deprecated
 * Add CodeMirror syntax highlighting Javascript src URLs to a page
 * @param $inline bool set to TRUE to return inline CSS / JS
 * @return bool
 */
function jrCore_add_code_mirror_js($inline = false)
{
    global $_conf;
    if ($inline) {
        if (!jrCore_get_flag('jrcore_code_mirror_inline_added')) {
            $out = "<style type=\"text/css\">\n";
            $out .= file_get_contents(APP_DIR . '/modules/jrCore/contrib/codemirror/lib/codemirror.css');
            $out .= "</style>\n<script>\n";
            $_sr = array('lib/codemirror.js', 'mode/htmlmixed/htmlmixed.js', 'mode/xml/xml.js', 'mode/javascript/javascript.js', 'mode/css/css.js', 'mode/clike/clike.js', 'mode/php/php.js', 'mode/smarty/smarty.js');
            foreach ($_sr as $src) {
                $out .= file_get_contents(APP_DIR . "/modules/jrCore/contrib/codemirror/{$src}");
            }
            $out .= '</script>';
            jrCore_set_flag('jrcore_code_mirror_inline_added', 1);
            return $out;
        }
    }
    else {
        if (!jrCore_get_flag('jrcore_code_mirror_js_added')) {
            jrCore_create_page_element('css_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.css"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/lib/codemirror.js"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/htmlmixed/htmlmixed.js"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/xml/xml.js"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/javascript/javascript.js"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/css/css.js"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/clike/clike.js"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/php/php.js"));
            jrCore_create_page_element('javascript_footer_href', array('source' => "{$_conf['jrCore_base_url']}/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js"));
            jrCore_set_flag('jrcore_code_mirror_js_added', 1);
        }
    }
    return true;
}
