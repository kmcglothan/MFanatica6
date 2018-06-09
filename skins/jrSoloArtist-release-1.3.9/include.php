<?php
/**
 * Jamroom jrSoloArtist skin
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
* Jamroom SoloArtist skin
 * @copyright 2003 - 2012 by The Jamroom Network - All Rights Reserved
 * @author Brian Johnson - brian@jamroom.net
 */

// We are never called directly
if (!defined('APP_DIR')) {
    exit;
}

/**
 * meta
 */
function jrSoloArtist_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrSoloArtist',
        'title'       => 'Solo Artist',
        'version'     => '1.3.9',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'Designed to hilight an individual artist',
        'license'     => 'jcl',
        'category'    => 'music'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSoloArtist_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'playlist.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'bundle.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'gallery.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'poll.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSoloArtist', 'mobile.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSoloArtist', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSoloArtist', 'jrSoloArtist.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSoloArtist', APP_DIR . '/skins/jrSoloArtist/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrSoloArtist', 'white');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrSoloArtist', 24);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrSoloArtist', 'jrAudio', 'jrAudio_solo_player');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrSoloArtist', 'jrVideo', 'jrVideo_solo_player');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrSoloArtist', 'jrPlaylist', 'jrPlaylist_solo_player');

    return true;
}
