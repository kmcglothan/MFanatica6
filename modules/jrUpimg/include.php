<?php
/**
 * Jamroom 5 Editor Image Upload module
 *
 * copyright 2003 - 2016
 * by The Jamroom Network
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
function jrUpimg_meta()
{
    $_tmp = array(
        'name'        => 'Editor Image Upload',
        'url'         => 'upimg',
        'version'     => '1.1.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Adds an image upload tool to the Embedded Media module in the editor',
        'doc_url'     => 'https://www.jamroom.net/the-jamroom-network/documentation/modules/292/editor-image-upload',
        'requires'    => 'jrEmbed',
        'license'     => 'mpl',
        'category'    => 'forms'
    );
    return $_tmp;
}

/**
 * init
 */
function jrUpimg_init()
{
    // Make sure our tab looks nice
    jrCore_register_event_listener('jrEmbed', 'embed_tabs', 'jrUpimg_embed_tabs_listener');
    jrCore_register_event_listener('jrEmbed', 'embed_variables', 'jrUpimg_embed_variables_listener');

    // Core support
    $_tmp = array(
        'label' => 'Show in Editor',
        'help'  => 'If checked, the &quot;Image Upload&quot; tab will show in the jrEmbed popup in the editor'
    );
    jrCore_register_module_feature('jrCore', 'quota_support', 'jrUpimg', 'on', $_tmp);

    jrCore_register_module_feature('jrSiteBuilder', 'widget', 'jrUpimg', 'widget_upimg', 'Uploaded Images');

    jrCore_register_module_feature('jrCore', 'javascript', 'jrUpimg', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'css', 'jrUpimg', 'slider.css');

    return true;
}

//------------------------------------
// EVENT LISTENERS
//------------------------------------

/**
 * We change the pagebreak on an embed item list
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUpimg_embed_tabs_listener($_data, $_user, $_conf, $_args, $event)
{
    $_ln                              = jrUser_load_lang_strings();
    $_data['tabs']['jrUpimg']['name'] = $_ln['jrUpimg'][1];
    return $_data;
}

/**
 * Add in image size selector when viewing a gallery list
 * @param $_data array incoming data array from jrCore_save_media_file()
 * @param $_user array current user info
 * @param $_conf array Global config
 * @param $_args array additional info about the module
 * @param $event string Event Trigger name
 * @return array
 */
function jrUpimg_embed_variables_listener($_data, $_user, $_conf, $_args, $event)
{
    $_lng = jrUser_load_lang_strings();
    $_tmp = jrImage_get_allowed_image_widths();
    foreach ($_tmp as $k => $v) {
        if (is_numeric($k)) {
            $_data['image_sizes'][$k] = $v;
        }
        if (!is_numeric($k)) {
            $_data['image_names'][$v] = $k;
        }
    }
    natsort($_data['image_sizes']);
    $_field = array(
        'name' => 'upimg_file',
        'text' => $_lng['jrUpimg'][2],
        'help' => $_lng['jrUpimg'][3],
        'type' => 'page'
    );
    $mxsize = jrUser_get_profile_home_key('quota_jrImage_max_image_size');
    $_field = jrCore_enable_meter_support($_field, 'png,jpg,gif,jpeg', $mxsize, false);
    jrCore_create_page_element('page', $_field);

    $_temp                 = jrCore_get_flag('jrcore_page_elements');
    $_temp['upload_token'] = jrCore_form_token_create();
    $_temp['show_search']  = false;
    $_temp['show_pager']   = false;
    return array_merge($_data, $_temp);
}

//------------------------------------
// WIDGETS
//------------------------------------

/**
 * Display CONFIG screen for IMAGE Editor Widget
 * @param $_post array Post info
 * @param $_user array User array
 * @param $_conf array Global Config
 * @param $_wg array Widget info
 * @return bool
 */
function jrUpimg_widget_upimg_config($_post, $_user, $_conf, $_wg)
{
    // Widget Content
    $_tmp = array(
        'name'     => 'upimg_list',
        'type'     => 'hidden',
        'validate' => 'printable'
    );
    jrCore_form_field_create($_tmp);

    // token for upload
    $_wg['tkn'] = jrCore_form_token_create();

    // header
    $html = jrCore_parse_template('widget_config_header.tpl', $_wg, 'jrUpimg');
    jrCore_page_custom($html);

    return true;
}

/**
 * Get Widget results from posted Config data
 * @param $_post array Post info
 * @return array
 */
function jrUpimg_widget_upimg_config_save($_post)
{
    return array('upimg_list' => $_post['upimg_list']);
}

/**
 * HTML Editor Widget DISPLAY
 * @param $_widget array Page Widget info
 * @return string
 */
function jrUpimg_widget_upimg_display($_widget)
{
    $_rt = false;
    if (isset($_widget['upimg_list'])) {
        // Create search params from $_post
        $_sp = array(
            'search' => array("_item_id IN {$_widget['upimg_list']}")
        );

        $_rt              = jrCore_db_search_items('jrUpimg', $_sp);
        $_rt['unique_id'] = jrCore_create_unique_string(6);
    }

    return jrCore_parse_template('widget_upimg_display.tpl', $_rt, 'jrUpimg');
}
