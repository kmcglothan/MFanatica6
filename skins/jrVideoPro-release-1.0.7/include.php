<?php
/**
 * Jamroom jrVideoPro skin
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
 * can be found in the "contrib" directory within this skin.
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
 * Jamroom MediaPro Skin
 * @copyright 2003 - 2016 by The Jamroom Network - All Rights Reserved
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * meta
 */
function jrVideoPro_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrVideoPro',
        'title'       => 'Video Pro',
        'version'     => '1.0.7',
        'developer'   => 'Talldude Networks, LLC, &copy;' . strftime('%Y'),
        'description' => 'Powerful Social Media features for Musicians and Artists',
        'license'     => 'jcl',
        'category'    => 'video'
    );
    return $_tmp;
}

/**
 * init
 */
function jrVideoPro_skin_init()
{
    global $_conf;

    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'acp.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'core_slidebar.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'override_tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'override_mobile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'player.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'animations.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'animations-ie-fix.css');

    if (isset($_conf['jrVideoPro_style']) && $_conf['jrVideoPro_style'] == '1') {
        jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'profile_dark.css');
        jrCore_register_module_feature('jrCore', 'css', 'jrVideoPro', 'skin_dark.css');
    }

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideoPro', 'jquery.mobile.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideoPro', 'jquery.scrollTo.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideoPro', 'jquery.sticky.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideoPro', 'jquery.slides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideoPro', 'jquery.mobile.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideoPro', 'jrVideoPro.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrVideoPro', APP_DIR . '/skins/jrVideoPro/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrVideoPro', '333333');

    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrVideoPro', 30);

    jrCore_register_module_feature('jrCore', 'media_player', 'jrVideoPro', 'jrVideoPro_video_player', 'video');

    // default players
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrVideoPro', 'jrVideo', 'jrVideoPro_video_player');

    // Our default media player skins
    if (isset($_conf['jrVideoPro_style']) && $_conf['jrVideoPro_style'] == '1') {
        jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrVideoPro', 'jrAudio', 'jrAudio_black_overlay_player');
        jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrVideoPro', 'jrPlaylist', 'jrPlaylist_black_overlay_player');
    }
    else {
        jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrVideoPro', 'jrAudio', 'jrAudio_gray_overlay_player');
        jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrVideoPro', 'jrPlaylist', 'jrPlaylist_gray_overlay_player');
    }


    jrCore_register_event_listener('jrCore', 'form_display', 'jrVideoPro_insert_field');

    return true;
}

/**
 * Get action stats
 * @param $params array
 * @param $smarty object
 * @return array|string
 */
function smarty_function_jrVideoPro_stats($params, $smarty)
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
function jrVideoPro_insert_field($_data)
{
    // Is this the jrProfile/settings form?
    if (isset($_data['form_view']) &&
        ($_data['form_view'] == 'jrVideo/create' || $_data['form_view'] == 'jrVideo/update' ||
            $_data['form_view'] == 'jrVideo/create_album' || $_data['form_view'] == 'jrVideo/update_album')
    ) {

        if (!isset($_data['video_category'])) {
            $_tmp = array(
                'name'          => 'video_category',
                'label'         => 'Video Category',
                'help'          => 'Enter a category for this video',
                'type'          => 'select_and_text',
                'validate'      => 'printable',
                'form_designer' => true
            );
            jrCore_form_field_create($_tmp);
        }

        if (!isset($_data['video_description'])) {
            $_tmp = array(
                'name'          => 'video_description',
                'label'         => 'Video Description',
                'help'          => 'Enter a category for this video',
                'type'          => 'editor',
                'validate'      => 'allowed_html',
                'form_designer' => true
            );
            jrCore_form_field_create($_tmp);
        }
    }

    if (isset($_data['form_view']) && $_data['form_view'] == 'jrProfile/settings') {

        $_tmp = array(
            'name'          => 'profile_header_image',
            'type'          => 'image',
            'label'         => 'Cover Image',
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

/**
 * ShareThis smarty template function
 * @param array $params Smarty function parameters
 * @param array $smarty current Smarty object
 * @return string
 */
function smarty_function_jrVideoPro_shareThis($params, $smarty)
{
    if (!isset($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    $_it = array();
    if (isset($params['item_id'])) {
        if (!jrCore_checktype($params['item_id'], 'number_nz')) {
            return jrCore_smarty_invalid_error('item_id');
        }
        $_it = jrCore_db_get_item($params['module'], $params['item_id']);
        if (!$_it) {
            return '';
        }
    }
    $out = jrShareThis_share_feature($params['module'], $_it, $params, $smarty);
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $out);
        return '';
    }
    return $out;
}
