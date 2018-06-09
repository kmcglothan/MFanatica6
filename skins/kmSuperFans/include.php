<?php
/**
 * Jamroom kmSuperFans skin
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
function kmSuperFans_skin_meta()
{
    $_tmp = array(
        'name'        => 'kmSuperFans',
        'title'       => 'SuperFans',
        'version'     => '1.0.11',
        'developer'   => 'Talldude Networks, LLC, &copy;' . strftime('%Y'),
        'description' => 'Powerful Social Media features for Musicians and Artists',
        'license'     => 'jcl',
        'category'    => 'music'
    );
    return $_tmp;
}

/**
 * init
 */
function kmSuperFans_skin_init()
{
    global $_conf;

    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'acp.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'core_slidebar.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'override_tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'override_mobile.css');
    jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'player.css');

    if (isset($_conf['kmSuperFans_style']) && $_conf['kmSuperFans_style'] == '1') {
        jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'profile_dark.css');
        jrCore_register_module_feature('jrCore', 'css', 'kmSuperFans', 'skin_dark.css');
    }

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'kmSuperFans', 'jquery.mobile.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'kmSuperFans', 'jquery.scrollTo.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'kmSuperFans', 'jquery.sticky.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'kmSuperFans', 'jquery.slides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'kmSuperFans', 'kmSuperFans.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'kmSuperFans', APP_DIR . '/skins/kmSuperFans/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    if (isset($_conf['kmSuperFans_style']) && $_conf['kmSuperFans_style'] == '1') {
        jrCore_register_module_feature('jrCore', 'icon_color', 'kmSuperFans', 'white');
    }
    else {
        jrCore_register_module_feature('jrCore', 'icon_color', 'kmSuperFans', 'black');
    }

    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'kmSuperFans', 30);

    // available players
    jrCore_register_module_feature('jrCore', 'media_player', 'kmSuperFans', 'kmSuperFans_audio_player', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'kmSuperFans', 'kmSuperFans_playlist_player', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'kmSuperFans', 'kmSuperFans_video_player', 'video');

    // default players
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'kmSuperFans', 'jrAudio', 'kmSuperFans_audio_player');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'kmSuperFans', 'jrVideo', 'kmSuperFans_video_player');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'kmSuperFans', 'jrPlaylist', 'kmSuperFans_playlist_player');

    jrCore_register_event_listener('jrCore', 'form_display', 'kmSuperFans_insert_field');

    return true;
}

/**
 * Get action stats
 * @param $params array
 * @param $smarty object
 * @return array|string
 */
function smarty_function_kmSuperFans_stats($params, $smarty)
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
function kmSuperFans_insert_field($_data)
{

    $_ln = jrUser_load_lang_strings();

    // Is this the jrProfile/settings form?
    if (isset($_data['form_view']) &&
        ($_data['form_view'] == 'jrVideo/create' || $_data['form_view'] == 'jrVideo/update' ||
            $_data['form_view'] == 'jrVideo/create_album' || $_data['form_view'] == 'jrVideo/update_album')
    ) {

        if (!isset($_data['video_category'])) {
            $_tmp = array(
                'name'          => 'video_category',
                'label'         => $_ln['kmSuperFans'][70], // Video Category
                'help'          => $_ln['kmSuperFans'][71], // Enter a category for this video
                'type'          => 'select_and_text',
                'validate'      => 'printable',
                'form_designer' => true
            );
            jrCore_form_field_create($_tmp);
        }
    }

    if (isset($_data['form_view']) && $_data['form_view'] == 'jrProfile/settings') {

        $_tmp = array(
            'name'          => 'profile_header_image',
            'type'          => 'image',
            'label'         => $_ln['kmSuperFans'][72], // 'Cover Image';
            'help'          => $_ln['kmSuperFans'][73], // 'Enter the home location for this profile'
            'image_delete'  => true,
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'          => 'profile_website',
            'type'          => 'text',
            'label'         => $_ln['kmSuperFans'][74], // Website
            'sublabel'      => $_ln['kmSuperFans'][75], // must include http://
            'help'          => 'Enter the home website for this profile',
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'          => 'profile_location',
            'type'          => 'text',
            'label'         => $_ln['kmSuperFans'][76], // Location
            'sublabel'      => $_ln['kmSuperFans'][77], // City, State
            'help'          => $_ln['kmSuperFans'][78], // 'Enter the home location for this profile',
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);
    }

    return $_data;
}

