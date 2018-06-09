<?php
/**
 * Jamroom jrFlashback skin
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
* Jamroom Flashback skin
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
function jrFlashback_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrFlashback',
        'title'       => 'Flashback',
        'version'     => '1.4.7',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Flashback skin for Jamroom',
        'license'     => 'jcl',
        'category'    => 'legacy'
    );
    return $_tmp;
}

/**
 * init
 */
function jrFlashback_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'gallery.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'bundle.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'action.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'poll.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'mobile.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrFlashback', 'playlist.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrFlashback', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrFlashback', 'jrFlashback.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrFlashback', APP_DIR . '/skins/jrFlashback/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrFlashback', 'white');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrFlashback', 16);
    // Hide module icons
    jrCore_register_module_feature('jrCore', 'module_icons', 'jrFlashback', 'show', false);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrFlashback', 'jrAudio', 'jrAudio_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrFlashback', 'jrVideo', 'jrVideo_player_dark');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrFlashback', 'jrPlaylist', 'jrPlaylist_player_dark');

    return true;
}
