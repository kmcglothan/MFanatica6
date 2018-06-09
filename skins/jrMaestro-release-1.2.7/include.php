<?php
/**
 * Jamroom jrMaestro skin
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
 * Jamroom jrMaestro skin
 * @copyright 2003 - 2017 by The Jamroom Network - All Rights Reserved
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * jrMaestro_meta
 */
function jrMaestro_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrMaestro',
        'title'       => 'Maestro',
        'version'     => '1.2.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The innovative skin from The Jamroom Network',
        'license'     => 'jcl',
        'category'    => 'music'
    );
    return $_tmp;
}

/**
 * jrMaestro_init
 * unlike with a module, init() is NOT called on each page load, but is
 * called when the core needs to rebuild CSS or Javascript for the skin
 */
function jrMaestro_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'acp.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'core_slidebar.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'override_tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'override_mobile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'animations.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'animations-ie-fix.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrMaestro', 'player.css');


    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMaestro', 'jquery.mobile.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMaestro', 'jquery.scrollTo.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMaestro', 'jquery.sticky.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMaestro', 'jquery.slides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMaestro', 'jrMaestro.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrMaestro', APP_DIR . '/skins/jrMaestro/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrMaestro', 'white');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrMaestro', 30);

    // available players
    jrCore_register_module_feature('jrCore', 'media_player', 'jrMaestro', 'jrMaestro_video_player', 'video');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrMaestro', 'jrMaestro_audio_player', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrMaestro', 'jrMaestro_beat_player', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrMaestro', 'jrMaestro_playlist_player', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrMaestro', 'jrMaestro_video_action_player', 'video');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrMaestro', 'jrMaestro_audio_action_player', 'audio');
    jrCore_register_module_feature('jrCore', 'media_player', 'jrMaestro', 'jrMaestro_playlist_action_player', 'audio');

    // default players
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMaestro', 'jrAudio', 'jrMaestro_audio_player');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMaestro', 'jrVideo', 'jrMaestro_video_player');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrMaestro', 'jrPlaylist', 'jrMaestro_playlist_player');

    jrCore_register_event_listener('jrCore', 'form_display', 'jrMaestro_insert_field');

    return true;
}

function smarty_function_jrMaestro_sort($params, &$smarty)
{
    return jrCore_parse_template($params['template'], $params, 'jrMaestro');
}

function smarty_function_jrMaestro_icon($params, $smarty)
{
    return jrCore_parse_template('icon.tpl', $params, 'jrMaestro');
}

/**
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_jrMaestro_breadcrumbs($params, $smarty)
{
    return jrCore_parse_template('breadcrumbs.tpl', $params, 'jrMaestro');
}

/**
 * @param $_args
 * @param $smarty
 * @return int
 */
function smarty_function_jrMaestro_followers($_args, $smarty)
{
    return $_data['followers'] = (int) jrCore_db_run_key_function('jrFollower', 'follow_profile_id', $_args['profile_id'], 'count');
}

/**
 * "Feedback" buttons under timeline entries
 * @param $params array
 * @param $smarty object
 * @return bool|string
 */
function smarty_function_jrMaestro_feedback_buttons($params, $smarty)
{
    if (!isset($params['module'])) {
        return jrCore_smarty_missing_error('module');
    }
    if (!isset($params['item'])) {
        return jrCore_smarty_missing_error('item');
    }
    if (!is_array($params['item'])) {
        return jrCore_smarty_invalid_error('item');
    }

    $show = false;
    if (jrCore_module_is_active('jrLike')) {
        $show = true;
    }
    if (jrCore_module_is_active('jrComment')) {
        $show = true;
    }
    if (jrCore_module_is_active('jrTags')) {
        $show = true;
    }
    if (jrCore_module_is_active('jrShareThis')) {
        $show = true;
    }
    if (jrCore_module_is_active('jrAction')) {
        $show = true;
    }
    if ($show) {
        $prefix                  = jrCore_db_get_prefix($params['module']);
        $params['comment_count'] = 0;
        if (isset($params['item']["{$prefix}_comment_count"])) {
            $params['comment_count'] = $params['item']["{$prefix}_comment_count"];
        }
        $params['rating_count'] = 0;
        if (isset($params['item']["{$prefix}_rating_overall_count"])) {
            $params['rating_count'] = $params['item']["{$prefix}_rating_overall_count"];
        }
        $params['tag_count'] = 0;
        if (isset($params['item']["{$prefix}_tag_count"])) {
            $params['tag_count'] = $params['item']["{$prefix}_tag_count"];
        }
        return jrCore_parse_template('feedback.tpl', $params, 'jrMaestro');
    }
    return false;
}

/**
 * Formats variables from a list item into
 * variables the index templates can understand
 *
 * @params array current ranking item passed
 * @item current list item
 * @module current list module
 */
function smarty_function_jrMaestro_process_item($params, &$smarty) {

    global $_conf;

    // get our item array
    $item = $params['item'];

    // get or module
    $module = $params['module'];

    // get our module url
    $murl = jrCore_get_module_url($params['module']);

    // get our prefix
    $prefix = jrCore_db_get_prefix($module);

    // lang
    $_ln = jrUser_load_lang_strings();

    // set up our return
    $res = array(
        'module'        => $module,
        'murl'          => $murl,
        '_item_id'      => $item["_item_id"],
        'title'         => $item["{$prefix}_title"],
        'title_url'     => $item["{$prefix}_title_url"],
        'prefix'        => $prefix,
        'album'         => $item["{$prefix}_album"],
        'category'      => strlen($item["{$prefix}_category"]) > 0 ? $item["{$prefix}_category"] : $item["{$prefix}_genre"],
        'image_type'    => "{$prefix}_image",
        'text'          => strlen($item["{$prefix}_text"]) > 0 ? strip_tags($item["{$prefix}_text"]) : strip_tags($item["{$prefix}_description"]),
        'url'           => $_conf['jrCore_base_url'] .'/' . $item['profile_url'] . '/' . $murl . '/' . $item['_item_id'] . '/' . $item["{$prefix}_title_url"],
        'price'         => $item["{$prefix}_file_item_price"],
        'read_more'     => $_ln['jrMaestro'][71]
    );

    switch($module) {
        case 'jrVideo':
            $res['read_more'] = $_ln['jrMaestro'][73];
            break;
    }

    if ( $module == 'jrProfile') {
        $res['_item_id'] = $item['_profile_id'];
        $res['title'] = $item['profile_name'];
        $res['title_url'] = $item['profile_url'];
        $res['text'] = $item['profile_bio'];
        $res['url'] = $_conf['jrCore_base_url'] . '/' . $item["profile_url"];
    }

    // return our new item
    if (!empty($params['assign'])) {
        $smarty->assign($params['assign'], $res);
        return '';
    }

    return $res;

}

/**
 * @param $params
 * @param $smarty
 * @return string
 */
function smarty_function_jrMaestro_clear_notifications($params, $smarty)
{
    return jrCore_parse_template('notifications_reset.tpl', $params, 'jrMaestro');
}

/**
 * @param $params
 * @param $smarty
 * @return array|string
 */
function smarty_function_jrMaestro_stats($params, $smarty)
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
function jrMaestro_insert_field($_data)
{
    // lang
    $_ln = jrUser_load_lang_strings();

    // Is this the jrProfile/settings form?
    if (isset($_data['form_view']) &&
        ($_data['form_view'] == 'jrAudio/create' || $_data['form_view'] == 'jrAudio/update' ||
            $_data['form_view'] == 'jrAudio/create_album' || $_data['form_view'] == 'jrAudio/update_album')
    ) {
        if (!isset($_data['audio_text'])) {
            $_tmp = array(
                'name'          => 'audio_text',
                'label'         => $_ln['jrMaestro'][149], // 'Audio Text',
                'help'          => $_ln['jrMaestro'][150], // 'Enter a description for this audio file',
                'type'          => 'editor',
                'default'       => '',
                'validate'      => 'allowed_html',
                'form_designer' => true
            );
            jrCore_form_field_create($_tmp);
        }

        if (!isset($_data['audio_lyrics'])) {
            $_tmp = array(
                'name'          => 'audio_lyrics',
                'label'         => $_ln['jrMaestro'][151], // 'Audio Lyrics',
                'help'          => $_ln['jrMaestro'][152], // 'Enter the lyrics for this audio song',
                'type'          => 'editor',
                'default'       => '',
                'validate'      => 'allowed_html',
                'form_designer' => true
            );
            jrCore_form_field_create($_tmp);
        }

    }

    if (isset($_data['form_view']) &&
        ($_data['form_view'] == 'jrVideo/create' || $_data['form_view'] == 'jrVideo/update' ||
            $_data['form_view'] == 'jrVideo/create_album' || $_data['form_view'] == 'jrVideo/update_album')
    ) {

        if (!isset($_data['video_text'])) {
            $_tmp = array(
                'name'          => 'video_text',
                'label'         => $_ln['jrMaestro'][153], // 'Video Text',
                'help'          => $_ln['jrMaestro'][154], // 'Enter a description of this video',
                'type'          => 'editor',
                'default'       => '',
                'validate'      => 'allowed_html',
                'form_designer' => true
            );
            jrCore_form_field_create($_tmp);
        }

        if (!isset($_data['video_category'])) {
            $_tmp = array(
                'name'          => 'video_category',
                'label'         => $_ln['jrMaestro'][155], // 'Video Category',
                'help'          => $_ln['jrMaestro'][156], // 'Enter a category for this video',
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
            'label'         => $_ln['jrMaestro'][157], // 'Cover Image',
            'help'          => $_ln['jrMaestro'][158], // 'Enter the home location for this profile',
            'image_delete'  => true,
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'          => 'profile_website',
            'type'          => 'text',
            'label'         => $_ln['jrMaestro'][159], // 'Website',
            'sublabel'      => $_ln['jrMaestro'][160], // 'must include http://',
            'help'          => $_ln['jrMaestro'][161], // 'Enter the home website for this profile',
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);

        $_tmp = array(
            'name'          => 'profile_location',
            'type'          => 'text',
            'label'         => $_ln['jrMaestro'][162], // 'Location',
            'sublabel'      => $_ln['jrMaestro'][163], // 'City, State',
            'help'          => $_ln['jrMaestro'][164], // 'Enter the home location for this profile',
            'form_designer' => true
        );
        jrCore_form_field_create($_tmp);
    }

    return $_data;
}

