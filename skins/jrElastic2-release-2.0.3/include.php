<?php
/**
 * Jamroom jrElastic2 skin
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
 * Jamroom Elastic 2 skin
 * @copyright 2003 - 2017 by The Jamroom Network - All Rights Reserved
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * meta
 */
function jrElastic2_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrElastic2',
        'title'       => 'Elastic 2',
        'version'     => '2.0.3',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Elastic 2 Skin - clean, responsive and easy to customize',
        'license'     => 'mpl',
        'category'    => 'social'
    );
    return $_tmp;
}

/**
 * init
 */
function jrElastic2_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'override_tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrElastic2', 'override_mobile.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrElastic2', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrElastic2', 'jrElastic2.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrElastic2', APP_DIR . '/skins/jrElastic2/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrElastic2', 'black');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrElastic2', 25);
    // Hide module icons
    jrCore_register_module_feature('jrCore', 'module_icons', 'jrElastic2', 'show', false);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrElastic2', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrElastic2', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrElastic2', 'jrPlaylist', 'jrPlaylist_player_dark');

    jrCore_register_event_listener('jrCore', 'form_display', 'jrElastic2_insert_field');

    return true;
}

/**
 * Get action stats
 * @param $params array
 * @param $smarty object
 * @return array|string
 */
function smarty_function_jrElastic2_stats($params, $smarty)
{
    // Enabled?
    if (!jrCore_module_is_active('jrAction')) {
        return '';
    }

    $out = array();
    if (jrCore_checktype($params['profile_id'], 'number_nz')) {
        $out['actions'] = (int) jrCore_db_run_key_function('jrAction', '_profile_id', $params['profile_id'], 'count');
    }

    // Trigger our action_stats event  (jrFollowers adds in 'following' and 'followers')
    $out = jrCore_trigger_event('jrAction', 'action_stats', $out, $params);

    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}

/**
 * @param $_data
 * @return mixed
 */
function jrElastic2_insert_field($_data)
{

    if (isset($_data['form_view']) && $_data['form_view'] == 'jrProfile/settings') {

        $_tmp = array(
            'name'          => 'profile_header_image',
            'type'          => 'image',
            'label'         => 'Banner Image',
            'help'          => 'Enter the home location for this profile',
            'image_delete'  => true,
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'          => 'profile_website',
            'type'          => 'text',
            'label'         => 'Website',
            'sublabel'      => 'must include http://',
            'help'          => 'Enter the home website for this profile',
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'          => 'profile_location',
            'type'          => 'text',
            'label'         => 'Location',
            'sublabel'      => 'City, State',
            'help'          => 'Enter the home location for this profile',
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);
    }

    return $_data;
}
