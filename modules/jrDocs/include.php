<?php
/**
 * Jamroom Documentation module
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

/**
 * meta
 */
function jrDocs_meta()
{
    $_tmp = array(
        'name'        => 'Documentation',
        'url'         => 'documentation',
        'version'     => '1.5.0',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Add detailed documentation creation capabilities to Profiles',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/594/documentation',
        'category'    => 'profiles',
        'license'     => 'jcl'
    );
    return $_tmp;
}

/**
 * init
 */
function jrDocs_init()
{
    global $_conf;
    // Allow admin to customize our forms
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrDocs', 'create');
    jrCore_register_module_feature('jrCore', 'designer_form', 'jrDocs', 'update');

    // Register our CSS and JS
    jrCore_register_module_feature('jrCore', 'css', 'jrDocs', 'jrDocs.css');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrDocs', 'jrDocs.js');

    // Core support
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrDocs', 'on');

    // We have fields that can be searched
    jrCore_register_module_feature('jrSearch', 'search_fields', 'jrDocs', 'doc_title,doc_content', 53);

    // We translate matched item_ids ito documentation group ids
    jrCore_register_event_listener('jrSearch', 'search_item_ids', 'jrDocs_search_item_ids_listener');

    // Core item buttons
    $_tmp = array(
        'title'  => 'search documentation button',
        'icon'   => 'search2',
        'active' => 'on'
    );
    jrCore_register_module_feature('jrCore', 'item_index_button', 'jrDocs', 'jrDocs_item_index_button', $_tmp);

    // Listeners
    jrCore_register_event_listener('jrCore', 'db_search_items', 'jrDocs_db_search_items_listener');
    jrCore_register_event_listener('jrCore', 'verify_module', 'jrDocs_verify_module_listener');

    // Profile tabs
    if (!isset($_conf['jrDocs_show_toc']) || $_conf['jrDocs_show_toc'] != 'off') {
        $_tmp = array(
            'label' => 64,
            'group' => 'all'
        );
        jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrDocs', 'default', $_tmp);
        $_tmp = array(
            'label' => 54,
            'group' => 'all'
        );
        jrCore_register_module_feature('jrProfile', 'profile_tab', 'jrDocs', 'contents', $_tmp);
    }

    // Custom ShareThis
    jrCore_register_event_listener('jrShareThis', 'get_item_info', 'jrDocs_get_item_info_listener');

    // Do not show comment entries for admin docs
    jrCore_register_event_listener('jrComment', 'add_to_timeline', 'jrDocs_add_to_timeline_listener');

    // format tags
    jrCore_register_event_listener('jrCore', 'format_string_display', 'jrDocs_format_string_display_listener');

    return true;
}

//---------------------------------------------------------
// SEARCH BUTTON
//---------------------------------------------------------

/**
 * Return "search" button for doc index
 * @param $module string Module name
 * @param $_item array Item Array
 * @param $_args Smarty function parameters
 * @param $smarty Smarty Object
 * @param $test_only - check if button WOULD be shown for given module
 * @return string
 */
function jrDocs_item_index_button($module, $_item, $_args, $smarty, $test_only = false)
{
    global $_post;
    if ($module == 'jrDocs') {
        if ($test_only) {
            return true;
        }
        if (isset($_post['_1']) && strlen($_post['_1']) > 0) {
            $_ln = jrUser_load_lang_strings();
            $_rt = array(
                'url'     => '#',
                'onclick' => "$('#doc_search').slideToggle(300, function(){ $('#doc_search_text').focus() });",
                'icon'    => 'search2',
                'alt'     => $_ln['jrCore'][8]
            );
            return $_rt;
        }
    }
    return false;
}

//-----------------------------------
// DOC SECTION FUNCTIONS
//-----------------------------------

/**
 * Custom Search function plugin for jrSearch
 * @param $search string Search String
 * @param $pagebreak int Page Break value
 * @param $page int Page Number in results
 * @return mixed bool|array
 */
function jrDocs_get_search_items($search, $pagebreak, $page)
{
    // First - get matching sections
    $_sc = array(
        'search'        => array(
            "doc_title like %{$search}% || doc_content like %{$search}%"
        ),
        'return_keys'   => array('doc_group_id'),
        'group_by'      => 'doc_group_id',
        'skip_triggers' => true,
        'limit'         => 1000
    );
    $_rt = jrCore_db_search_items('jrDocs', $_sc);
    if (is_array($_rt) && is_array($_rt['_items'])) {
        $_id = array();
        foreach ($_rt['_items'] as $_sub) {
            $_id[] = (int) $_sub['doc_group_id'];
        }
        if (count($_id) > 0) {
            // Get doc leaders
            $_sc = array(
                'search'                       => array(
                    "_item_id in " . implode(',', $_id)
                ),
                'exclude_jrProfile_quota_keys' => true,
                'pagebreak'                    => $pagebreak,
                'page'                         => $page
            );
            return jrCore_db_search_items('jrDocs', $_sc);
        }
    }
    return false;
}

/**
 * Form section for documentation "text" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_text($_post, $_user, $_conf, $_item = null)
{
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Editor option
    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 15,
        'help'     => 16,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Form section for documentation "text_and_image" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_text_and_image($_post, $_user, $_conf, $_item = null)
{
    $_lng = jrUser_load_lang_strings();

    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    // Editor option
    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 15,
        'help'     => 16,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'doc_image',
        'label'    => 17,
        'help'     => 18,
        'text'     => 19,
        'type'     => 'image',
        'value'    => $_item,
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'doc_image_url',
        'label'    => 74,
        'help'     => 75,
        'type'     => 'text',
        'validate' => 'url',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_loc = array(
        'left'  => $_lng['jrDocs'][20],
        'right' => $_lng['jrDocs'][21]
    );
    $_tmp = array(
        'name'     => 'doc_image_float',
        'label'    => 22,
        'help'     => 23,
        'type'     => 'select',
        'options'  => $_loc,
        'default'  => 'right',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_siz = array();
    foreach (jrImage_get_allowed_image_widths() as $k => $v) {
        if (is_numeric($k)) {
            unset($_siz[$k]);
        }
        else {
            if ($k == 'original') {
                $_siz[$k] = $k;
            }
            else {
                $_siz[$k] = "{$k} ({$v}px)";
            }
        }
    }
    $_tmp = array(
        'name'     => 'doc_image_display_size',
        'label'    => 24,
        'help'     => 25,
        'type'     => 'select',
        'options'  => $_siz,
        'default'  => 'medium',
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Form section for documentation "screenshot" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_screenshot($_post, $_user, $_conf, $_item = null)
{
    global $_conf;
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'doc_image',
        'label'    => 26,
        'help'     => 27,
        'text'     => 19,
        'type'     => 'image',
        'value'    => $_item,
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'doc_image_url',
        'label'    => 74,
        'help'     => 75,
        'type'     => 'text',
        'validate' => 'url',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 28,
        'help'     => 29,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => false
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Form section for documentation "downloadable_file" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_downloadable_file($_post, $_user, $_conf, $_item = null)
{
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    if (!isset($_conf['jrDocs_allowed_file_types']) || strlen($_conf['jrDocs_allowed_file_types']) === 0) {
        $_conf['jrDocs_allowed_file_types'] = 'zip,png,jpg,pdf';
    }
    $_tmp = array(
        'name'       => 'doc_file',
        'label'      => 30,
        'help'       => 31,
        'text'       => 32,
        'type'       => 'file',
        'value'      => $_item,
        'extensions' => $_conf['jrDocs_allowed_file_types'],
        'required'   => (isset($_item) && is_array($_item)) ? false : true
    );
    jrCore_form_field_create($_tmp);

    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 33,
        'help'     => 34,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => false
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Form section for documentation "hint" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_hint($_post, $_user, $_conf, $_item = null)
{
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 15,
        'help'     => 16,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Form section for documentation "warning" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_warning($_post, $_user, $_conf, $_item = null)
{
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 15,
        'help'     => 16,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Form section for documentation "code" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_code($_post, $_user, $_conf, $_item = null)
{
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 15,
        'help'     => 16,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'doc_code',
        'label'    => 35,
        'help'     => 36,
        'type'     => 'textarea',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    jrCore_form_field_create($_tmp);

    $_lng = array(
        'Php'     => 'PHP',
        'JScript' => 'Javascript',
        'Xml'     => 'HTML',
        'Css'     => 'CSS'
    );
    $_tmp = array(
        'name'     => 'doc_code_language',
        'label'    => 37,
        'help'     => 38,
        'type'     => 'select',
        'options'  => $_lng,
        'required' => true
    );
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * Form section for documentation "youtube" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_youtube($_post, $_user, $_conf, $_item = null)
{
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 2,
        'help'     => 14,
        'type'     => 'text',
        'validate' => 'string',
        'required' => false
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'doc_youtube',
        'label'    => 55,
        'help'     => 56,
        'type'     => 'text',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    jrCore_form_field_create($_tmp);

    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 57,
        'help'     => 58,
        'type'     => $type,
        'validate' => 'allowed_html',
        'required' => false
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);
    return true;
}

/**
 * get just the youtube ID if its a full url
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_youtube_save($_post, $_user, $_conf, $_item = null)
{
    $_sv = jrCore_form_get_save_data('jrDocs', 'section_update', $_post);
    if (function_exists('jrYouTube_extract_id')) {
        // Get our YouTube ID from the input
        $yid = jrYouTube_extract_id($_post['doc_youtube']);
        if (!isset($yid) || !preg_match('/[a-zA-Z0-9_-]/', $yid)) {
            jrCore_set_form_notice('error', 'unable to extract the YouTube ID from the given input - please try again');
        }
        else {
            $_sv['doc_youtube'] = $yid;
        }
    }
    return $_sv;
}

/**
 * Form section for documentation "function parameters" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return bool
 */
function jrDocs_section_function_definition($_post, $_user, $_conf, $_item = null)
{
    $_lng = jrUser_load_lang_strings();
    $_tmp = array(
        'name'     => 'doc_title',
        'label'    => 39,
        'help'     => 40,
        'type'     => 'text',
        'validate' => 'string',
        'required' => (isset($_item) && is_array($_item)) ? false : true
    );
    jrCore_form_field_create($_tmp);

    $type = (isset($_conf['jrDocs_editor']) && $_conf['jrDocs_editor'] == 'on') ? 'editor' : 'textarea';
    $_tmp = array(
        'name'     => 'doc_content',
        'label'    => 41,
        'help'     => 42,
        'type'     => 'textarea',
        'validate' => 'allowed_html',
        'required' => false
    );
    if ($type == 'textarea' && is_array($_item) && isset($_item['doc_content'])) {
        $rows = substr_count($_item['doc_content'], "\n");
        $rows += ceil(strlen($_item['doc_content']) / 75);
        if ($rows > 6) {
            $_tmp['style'] = 'height: unset';
            $_tmp['rows']  = $rows;
        }
    }
    jrCore_form_field_create($_tmp);

    $_ret = array(
        'int'    => 'int',
        'string' => 'string',
        'float'  => 'float',
        'array'  => 'array',
        'object' => 'object',
        'bool'   => 'boolean',
        'null'   => 'null',
        'mixed'  => 'mixed'
    );
    $_tmp = array(
        'name'     => 'doc_returns',
        'label'    => 43,
        'help'     => 44,
        'type'     => 'select',
        'options'  => $_ret,
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    $_tmp = array(
        'name'     => 'doc_param_list',
        'label'    => 60,
        'help'     => 61,
        'type'     => 'checkbox',
        'default'  => 'on',
        'required' => true
    );
    jrCore_form_field_create($_tmp);

    jrCore_page_divider();

    if (isset($_item['doc_parameters']{1})) {
        $_item['doc_parameters'] = json_decode($_item['doc_parameters'], true);
    }

    $dat             = array();
    $dat[1]['title'] = $_lng['jrDocs'][45];
    $dat[1]['width'] = '15%';
    $dat[2]['title'] = $_lng['jrDocs'][46];
    $dat[2]['width'] = '15%';
    $dat[3]['title'] = $_lng['jrDocs'][47];
    $dat[3]['width'] = '15%';
    $dat[4]['title'] = $_lng['jrDocs'][48];
    $dat[4]['width'] = '5%';
    $dat[5]['title'] = $_lng['jrDocs'][49];
    $dat[5]['width'] = '47%';
    $dat[6]['title'] = '&times;';
    $dat[6]['width'] = '3%';
    jrCore_page_table_header($dat, 'doc_param_table');

    $num = 0;
    if (is_array($_item['doc_parameters'])) {
        foreach ($_item['doc_parameters'] as $k => $_param) {
            $dat             = array();
            $dat[1]['title'] = '<input type="text" name="old_param_name_' . $k . '" value="' . $_param['name'] . '" class="form_text">';
            $dat[2]['title'] = '<input type="text" name="old_param_type_' . $k . '" value="' . $_param['type'] . '" class="form_text">';
            $dat[3]['title'] = '<input type="text" name="old_param_default_' . $k . '" value="' . jrCore_entity_string($_param['default']) . '" class="form_text">';

            $dat[4]['title'] = '<input type="hidden" name="old_param_required_' . $k . '" value="off">';
            if (isset($_param['required']) && $_param['required'] == 'on') {
                $dat[4]['title'] .= '<input type="checkbox" name="old_param_required_' . $k . '" class="form_checkbox" checked="checked">';
            }
            else {
                $dat[4]['title'] .= '<input type="checkbox" name="old_param_required_' . $k . '" class="form_checkbox">';
            }
            $dat[4]['class'] = 'center';
            $dat[5]['title'] = '<input type="text" name="old_param_description_' . $k . '" value="' . jrCore_entity_string($_param['description']) . '" class="form_text" style="width:99%;box-sizing: border-box">';
            $dat[6]['title'] = jrCore_page_button("d{$_param['name']}", '&times;', "jrCore_window_location('{$_conf['jrCore_base_url']}/{$_post['module_url']}/section_parameter_delete/id={$_item['_item_id']}/parameter=" . jrCore_url_encode_string($_param['name']) . "')");
            jrCore_page_table_row($dat);
            $num = $k;
        }
    }

    // New Parameters
    $num++;
    foreach (range($num, $num + 4) as $k) {
        $dat             = array();
        $dat[1]['title'] = '<input type="text" name="new_param_name_' . $k . '" class="form_text">';
        $dat[2]['title'] = '<input type="text" name="new_param_type_' . $k . '" class="form_text">';
        $dat[3]['title'] = '<input type="text" name="new_param_default_' . $k . '" class="form_text">';
        $dat[4]['title'] = '<input type="checkbox" name="new_param_required_' . $k . '" class="form_checkbox">';
        $dat[4]['class'] = 'center';
        $dat[5]['title'] = '<input type="text" name="new_param_description_' . $k . '" class="form_text">';
        jrCore_page_table_row($dat);
    }
    jrCore_page_table_footer();
    return true;
}

/**
 * save function for documentation "parameters" block
 * @param $_post array Global $_REQUEST array
 * @param $_user array Current viewing user information
 * @param $_conf array Global config
 * @param $_item array Existing item info
 * @return mixed
 */
function jrDocs_section_function_definition_save($_post, $_user, $_conf, $_item = null)
{
    // Get our existing parameters
    $_pr = array();
    if (isset($_item['doc_parameters']{1})) {
        $_pr = json_decode($_item['doc_parameters'], true);
    }
    // Update any existing parameters
    foreach ($_post as $k => $v) {
        if (strpos($k, 'old_param_') === 0) {
            list(, , $nam, $key) = explode('_', $k);
            $_pr[$key][$nam] = $v;
            unset($_post[$k]);
        }
    }
    // We need to create our doc_parameters field
    foreach ($_post as $k => $v) {
        if (strpos($k, 'new_param_') === 0) {
            list(, , $nam, $key) = explode('_', $k);
            if (!isset($_pr[$key])) {
                $_pr[$key] = array();
            }
            $_pr[$key][$nam] = $v;
            unset($_post[$k]);
        }
    }
    if (isset($nam)) {
        // Cleanup empty items
        foreach ($_pr as $k => $_v) {
            if (strlen($_v['name']) === 0) {
                unset($_pr[$k]);
            }
        }
        $_pr                     = array_values($_pr);
        $_post['doc_parameters'] = json_encode($_pr);
    }
    // Cleanup
    foreach ($_post as $k => $v) {
        if (strpos($k, 'doc_') !== 0) {
            unset($_post[$k]);
        }
    }
    return $_post;
}

//----------------------
// EVENT LISTENERS
//----------------------

/**
 * Prevent comment timeline entries from showing up for admin docs
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrDocs_add_to_timeline_listener($_data, $_user, $_conf, $_args, $event)
{
    if (isset($_data['doc_group']) && ($_data['doc_group'] == 'admin' || $_data['doc_group'] == 'master')) {
        $_data['add_to_timeline'] = false;
    }
    return $_data;
}

/**
 * add the chapter order into docs searches.
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrDocs_db_search_items_listener($_data, $_user, $_conf, $_args, $event)
{
    if (jrCore_is_view_request() && $_args['module'] == 'jrDocs' && isset($_data['_items'])) {
        // get the chapter orders for this category_url on this profile.
        $tbl = jrCore_db_table_name('jrDocs', 'chapter');
        $req = "SELECT * FROM {$tbl}";
        $_rt = jrCore_db_query($req, 'NUMERIC');
        if (!$_rt || !is_array($_rt)) {
            return $_data;
        }
        // chapters
        $_chapters = array();
        foreach ($_rt as $k => $v) {
            $_chapters[$v['chapter_profile_id']][$v['chapter_category_url']] = $v['chapter_order'];
        }
        // add to item
        foreach ($_data['_items'] as $k => $_v) {
            if (isset($_v['doc_category_url']) && isset($_chapters["{$_v['_profile_id']}"]["{$_v['doc_category_url']}"])) {
                $_data['_items'][$k]['chapter_order'] = $_chapters["{$_v['_profile_id']}"]["{$_v['doc_category_url']}"];
            }
        }
    }
    return $_data;
}

/**
 * Verify module listener to add make sure all the chapters are in the chapter order table.
 * @param $_data array Array of information from trigger
 * @param $_user array Current user
 * @param $_conf array Global Config
 * @param $_args array additional parameters passed in by trigger caller
 * @param $event string Triggered Event name
 * @return array
 */
function jrDocs_verify_module_listener($_data, $_user, $_conf, $_args, $event)
{
    // Setup Chapters
    if (jrCore_db_get_datastore_item_count('jrDocs') > 0 && jrCore_db_number_rows('jrDocs', 'chapter') === 0) {
        // get all the chapters
        $_sp           = array(
            'search'                       => array(
                "_profile_id > 0"
            ),
            'order_by'                     => array(
                'doc_category' => 'asc'
            ),
            'group_by'                     => '_profile_id, doc_category_url',
            'return_keys'                  => array('_profile_id', 'doc_category', 'doc_category_url', 'profile_url'),
            'privacy_check'                => false,
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'limit'                        => 5000
        );
        $_rt           = jrCore_db_search_items('jrDocs', $_sp);
        $_category_url = array();
        if (isset($_rt['_items']) && is_array($_rt['_items'])) {
            foreach ($_rt['_items'] as $item) {
                if (!isset($_category_url["{$item['_profile_id']}"])) {
                    $_category_url["{$item['_profile_id']}"] = array();
                }
                $_category_url["{$item['_profile_id']}"]["{$item['doc_category_url']}"] = $item['doc_category_url'];
            }
        }

        // get the correct correct order with new at the bottom.
        $tbl       = jrCore_db_table_name('jrDocs', 'chapter');
        $req       = "SELECT * FROM {$tbl} ORDER BY `chapter_order` asc";
        $_chapter  = jrCore_db_query($req, 'NUMERIC');
        $_existing = array();
        if (is_array($_chapter)) {
            foreach ($_chapter as $c) {
                $_existing[$c['chapter_profile_id']] = $c['chapter_category_url'];

            }
        }

        // remove all existing from total
        foreach ($_existing as $pid => $cat_url) {
            if (isset($_category_url[$pid][$cat_url])) {
                // already got this one, don't need to add again so unset
                unset($_category_url[$pid][$cat_url]);
            }
        }

        // Add whats left into the chapter database at the bottom
        $tbl = jrCore_db_table_name('jrDocs', 'chapter');
        $ord = 900;
        if (!empty($_category_url)) {
            $_rq = array();
            foreach ($_category_url as $pid => $_urls) {
                if (isset($_urls) && is_array($_urls)) {
                    foreach ($_urls as $url) {
                        $_rq[] = "('" . jrCore_db_escape($pid) . "','" . jrCore_db_escape($url) . "','{$ord}')";
                        $ord++;
                    }
                }
            }
            if (count($_rq) > 0) {
                $req = "INSERT INTO {$tbl} (`chapter_profile_id`,`chapter_category_url`,`chapter_order`) VALUES " . implode(',', $_rq) . " ON DUPLICATE KEY UPDATE `chapter_order` = VALUES(`chapter_order`)";
                jrCore_db_query($req);
            }
        }
    }

    // Setup missing doc_group keys
    $_sp = array(
        'search'        => array(
            'doc_section_type = header',
            'doc_group != all'
        ),
        'skip_triggers' => true,
        'return_keys'   => array('_item_id', 'doc_group'),
        'limit'         => 5000
    );
    $_sp = jrCore_db_search_items('jrDocs', $_sp);
    if ($_sp && is_array($_sp) && isset($_sp['_items'])) {
        $_id = array();
        foreach ($_sp['_items'] as $_i) {
            if (!isset($_i['doc_group']) || strlen($_i['doc_group']) === 0) {
                $_id["{$_i['_item_id']}"] = array('doc_group' => 'all');
            }
        }
        if (count($_id) > 0) {
            jrCore_db_update_multiple_items('jrDocs', $_id);
        }
    }

    // Make sure our "doc_show_related is NOT in form designer
    jrCore_delete_designer_form_field('jrDocs', 'create', 'doc_show_related');
    jrCore_delete_designer_form_field('jrDocs', 'update', 'doc_show_related');

    return $_data;
}

/**
 * Get possible groups for a user
 * @return array
 */
function jrDocs_get_groups()
{
    if (jrUser_is_admin()) {
        $_opt = array(
            'all'     => '(group) All Users (including logged out)',
            'admin'   => '(group) Profile Admins',
            'power'   => '(group) Power Users',
            'user'    => '(group) Logged In Users',
            'visitor' => '(group) Logged Out Users'
        );
        if (jrUser_is_master()) {
            $_opt['master'] = '(group) Master Admins';
        }
        $_qta = jrProfile_get_quotas();
        if (isset($_qta) && is_array($_qta)) {
            foreach ($_qta as $qid => $qname) {
                $_opt[$qid] = "(quota) {$qname}";
            }
        }
    }
    else {
        $_lng = jrUser_load_lang_strings();
        $_opt = array(
            'owner'   => $_lng['jrDocs'][68],
            'all'     => $_lng['jrDocs'][69],
            'user'    => $_lng['jrDocs'][70],
            'visitor' => $_lng['jrDocs'][71]
        );
    }
    return $_opt;
}

/**
 * Custom info array for ShareThis
 * @param array $_data incoming data array from jrCore_save_media_file()
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrDocs_get_item_info_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if (isset($_post['_2']) && jrCore_checktype($_post['_2'], 'number_nz')) {

        $_it = jrCore_db_get_item('jrDocs', $_post['_2'], true);
        if ($_it && is_array($_it)) {
            $_sp = array(
                'search'         => array(
                    "doc_group_id = {$_post['_2']}"
                ),
                'order_by'       => array(
                    "doc_section_order" => "numerical_asc"
                ),
                'skip_triggers'  => true,
                'privacy_check'  => false,
                'ignore_pending' => true
            );
            $_rt = jrCore_db_search_items('jrDocs', $_sp);
            if (isset($_rt['_items']) && is_array($_rt['_items'])) {
                foreach ($_rt['_items'] as $_section) {
                    if ($_section['doc_section_type'] == 'text') {
                        $_it['page_description'] = jrCore_entity_string(str_replace(array("\n", "\r"), '', jrCore_strip_html(smarty_modifier_jrCore_format_string($_section['doc_content'], 0))));
                        break;
                    }
                }
            }
            return $_it;
        }
    }
    return $_data;
}

/**
 * Get Group_IDs based on _item_ids matched in search
 * @param array $_data incoming data
 * @param array $_user current user info
 * @param array $_conf Global config
 * @param array $_args additional info about the module
 * @param string $event Event Trigger name
 * @return array
 */
function jrDocs_search_item_ids_listener($_data, $_user, $_conf, $_args, $event)
{
    // our $_data is an array of _item_ids that matched our FULL TEXT search
    // We have to get the GROUP_IDs and return those instead
    if ($_data && is_array($_data) && count($_data) > 0) {
        $_sc = array(
            'search'        => array(
                '_item_id in ' . implode(',', $_data)
            ),
            'return_keys'   => array('doc_group_id'),
            'group_by'      => 'doc_group_id',
            'skip_triggers' => true,
            'limit'         => count($_data)
        );
        $_rt = jrCore_db_search_items('jrDocs', $_sc);
        if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
            $_id = array();
            foreach ($_rt['_items'] as $_sub) {
                $_id[] = (int) $_sub['doc_group_id'];
            }
            if (count($_id) > 0) {
                $_sc = array(
                    'search'    => array(
                        '_item_id in ' . implode(',', $_id)
                    ),
                    'page'      => (int) $_args['page'],
                    'pagebreak' => (int) $_args['pagebreak']
                );
                return jrCore_db_search_items('jrDocs', $_sc);
            }
        }
    }
    return $_data;
}

/**
 * Scan Text in docs and replace links to related tagged docs
 * @param $_data array
 * @param $_user array
 * @param $_conf array
 * @param $_args array
 * @param $event string
 * @return mixed
 */
function jrDocs_format_string_display_listener($_data, $_user, $_conf, $_args, $event)
{
    global $_post;
    if ($_post['module'] == 'jrDocs' && isset($_conf['jrDocs_show_related']) && $_conf['jrDocs_show_related'] == 'on' && !jrCore_get_flag('jrdocs_hide_show_related')) {
        if (strlen($_data['string']) > 3 && jrCore_module_is_active('jrTags')) {
            $_replace = jrDocs_replacement_tags();
            if ($_replace && is_array($_replace)) {
                $_data['string'] = strtr($_data['string'], $_replace);
            }
        }
    }
    return $_data;
}

/*
 * find any existing tags in this string.
 */
function jrDocs_replacement_tags()
{
    global $_post, $_user;
    $key = 'jrDocs_tag_replace';
    if (!$_replace = jrCore_is_cached('jrDocs', $key, true, false)) {

        // Get all current tags
        $_rt = array(
            'search'         => array(
                'doc_section_type = header',
                'doc_tags like %'
            ),
            'return_keys'    => array('_item_id', 'doc_tags'),
            'limit'          => 10000,
            'ignore_pending' => true,
            'skip_triggers'  => true,
            'privacy_check'  => false,
            'quota_check'    => false
        );

        if (!jrUser_is_admin()) {
            $grp = 'all,visitor';
            if (jrUser_is_logged_in()) {
                $grp = "all,user,{$_user['profile_quota_id']}";
                if (jrUser_is_power_user()) {
                    $grp .= ',power';
                }
                if (isset($_post['_profile_id']) && jrProfile_is_profile_owner($_post['_profile_id'])) {
                    $grp .= ',owner';
                }
            }
            if ($grp) {
                $_rt['search'][] = "doc_group in {$grp}";
            }
        }
        $_rt = jrCore_db_search_items('jrDocs', $_rt);
        if ($_rt && is_array($_rt) && is_array($_rt['_items'])) {
            $_replace = array();
            foreach ($_rt['_items'] as $_doc) {
                // Don't show the page we are looking at
                if (isset($_post['_2']) && is_numeric($_post['_2']) && $_post['_2'] == $_doc['_item_id']) {
                    continue;
                }
                foreach (explode(',', trim($_doc['doc_tags'], ',')) as $tag) {
                    $tag = trim($tag);
                    if (isset($tag{0}) && !isset($_replace[$tag])) {
                        foreach (array($tag, ucwords($tag), ucfirst($tag), jrCore_str_to_lower($tag), jrCore_str_to_upper($tag)) as $t) {
                            $_replace[' ' . $t . ' '] = ' <span class="docs_related" data-tag="' . addslashes($t) . '"><span>' . $t . '</span></span> ';
                        }
                    }
                }
            }
        }
        if (!$_replace || count($_replace) === 0) {
            $_replace = 'no_results';
        }
        jrCore_add_to_cache('jrDocs', $key, $_replace, 0, 0, true, false);
    }
    if (is_array($_replace)) {
        return $_replace;
    }
    return false;
}

/**
 * Get the documents in a list that are visible to the viewer
 * @param array $_items
 * @param int $doc_profile_id
 * @return mixed
 */
function jrDocs_get_docs_visible_to_viewer($_items, $doc_profile_id)
{
    if (!is_array($_items) || jrUser_is_master()) {
        return $_items;
    }

    $quota = (int) jrUser_get_profile_home_key('profile_quota_id');
    $pid   = (int) $doc_profile_id;
    foreach ($_items as $k => $_item) {

        $add = false;
        if (isset($_item['doc_group'])) {

            // Are we allowing ALL viewers?
            if ($_item['doc_group'] == 'all' || strpos(' ' . $_item['doc_group'], 'all')) {
                $add = true;
            }
            else {
                // No - we have specific view restrictions
                if (strpos($_item['doc_group'], ',')) {
                    $_tmp = explode(',', $_item['doc_group']);
                }
                else {
                    $_tmp = array($_item['doc_group']);
                }
                if (!$_tmp || !is_array($_tmp)) {
                    unset($_items[$k]);
                    continue;
                }

                foreach ($_tmp as $group) {
                    $group = trim($group);
                    switch ($group) {

                        case 'all':
                            $add = true;
                            break;

                        case 'admin':
                            if (jrUser_is_admin()) {
                                $add = true;
                                break 2;
                            }
                            break;

                        case 'power':
                            if (jrUser_is_power_user()) {
                                $add = true;
                                break 2;
                            }
                            break;

                        case 'owner':
                            if (jrProfile_is_profile_owner($pid)) {
                                $add = true;
                                break 2;
                            }
                            break;

                        case 'user':
                            if (jrUser_is_logged_in()) {
                                $add = true;
                                break 2;
                            }
                            break;

                        case 'visitor':
                            if (!jrUser_is_logged_in()) {
                                $add = true;
                                break 2;
                            }
                            break;

                        default:
                            if (jrCore_checktype($group, 'number_nz') && $group == $quota) {
                                $add = true;
                                break 2;
                            }
                    }
                }
            }
        }
        if (!$add) {
            unset($_items[$k]);
        }
    }
    return $_items;
}

//----------------------
// SMARTY FUNCTIONS
//----------------------

/**
 * {jrDocs_categories}
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrDocs_categories($params, $smarty)
{
    // get all the categories for this users docs
    if (isset($params['profile_id']) && jrCore_checktype($params['profile_id'], 'number_nz')) {
        $_sp = array(
            'search'                       => array(
                'doc_category like %',
                "_profile_id = {$params['profile_id']}"
            ),
            'order_by'                     => array(
                'doc_category' => 'asc'
            ),
            'group_by'                     => 'doc_category_url',
            'return_keys'                  => array('_profile_id', 'doc_category', 'doc_category_url', 'doc_group', 'profile_url'),
            'privacy_check'                => false,
            'exclude_jrUser_keys'          => true,
            'exclude_jrProfile_quota_keys' => true,
            'limit'                        => 500
        );
        $_rt = jrCore_db_search_items('jrDocs', $_sp);
        if ($_rt && is_array($_rt) && isset($_rt['_items'])) {

            // Get only the items this user can view
            $_rt['_items'] = jrDocs_get_docs_visible_to_viewer($_rt['_items'], $params['profile_id']);

            // get the correct correct order with new at the bottom.
            $pid  = (int) $params['profile_id'];
            $tbl  = jrCore_db_table_name('jrDocs', 'chapter');
            $req  = "SELECT * FROM {$tbl} WHERE `chapter_profile_id` = {$pid} ORDER BY (`chapter_order` + 0) ASC";
            $_ord = jrCore_db_query($req, 'chapter_category_url');

            $_docs = array();
            foreach ($_rt['_items'] as $item) {
                $_docs["{$item['doc_category_url']}"] = $item;
            }

            $_out = array();
            foreach ($_ord as $cat_url => $stuff) {
                if (isset($_docs[$cat_url])) {
                    $_out[$cat_url] = $_docs[$cat_url];
                    unset($_docs[$cat_url]);
                }
            }
            $_out = array_merge($_out, $_docs);

            if (!empty($params['assign'])) {
                $smarty->assign($params['assign'], $_out);
            }
        }
    }
    return '';
}

/**
 * Takes the current profile URL location and returns the category menu
 * {jrDocs_menu}
 * @param $params array Smarty function params
 * @param $smarty object Smarty Object
 * @return string
 */
function smarty_function_jrDocs_menu($params, $smarty)
{
    global $_post;
    if (!isset($_post['_1']) || strlen($_post['_1']) === 0) {
        return '';
    }
    $_sp = array(
        'search'        => array(
            "doc_category_url = {$_post['_1']}",
        ),
        'order_by'      => array(
            'doc_order' => 'numerical_asc'
        ),
        'limit'         => 1000,
        'skip_triggers' => true
    );
    if (isset($params['profile_id'])) {
        $_sp['search'][] = "_profile_id = {$params['profile_id']}";
    }
    $_rt = jrCore_db_search_items('jrDocs', $_sp);
    if ($_rt && is_array($_rt) && isset($_rt['_items'])) {
        $_rt['_items'] = jrDocs_get_docs_visible_to_viewer($_rt['_items'], $params['profile_id']);
    }
    return jrCore_parse_template('menu_items.tpl', $_rt, 'jrDocs');
}
