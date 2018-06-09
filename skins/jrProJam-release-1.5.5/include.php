<?php
/**
 * Jamroom jrProJam skin
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
 * Jamroom 5 ProJam skin
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
function jrProJam_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrProJam',
        'title'       => 'Pro Jam Dark',
        'version'     => '1.5.5',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Pro Jam for Jamroom 5 (dark version)',
        'license'     => 'jcl',
        'category'    => 'music'
    );
    return $_tmp;
}

/**
 * init
 */
function jrProJam_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'gallery.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'mobile.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'player.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'playlist.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'buttons.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'bundle.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrProJam', 'rating.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProJam', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProJam', 'jrProJam.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrProJam', APP_DIR . '/skins/jrProJam/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrProJam', 'white');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrProJam', 18);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProJam', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProJam', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrProJam', 'jrPlaylist', 'jrPlaylist_player_dark');

    return true;
}
