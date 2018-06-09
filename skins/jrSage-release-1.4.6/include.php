<?php
/**
 * Jamroom jrSage skin
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
* Jamroom Sage skin
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
function jrSage_skin_meta()
{
    $_tmp = array(
        'name'        => 'jrSage',
        'title'       => 'Sage',
        'version'     => '1.4.6',
        'developer'   => 'The Jamroom Network, &copy;' . strftime('%Y'),
        'description' => 'The Sage skin, for Jamroom',
        'license'     => 'jcl',
        'category'    => 'legacy'
    );
    return $_tmp;
}

/**
 * init
 */
function jrSage_skin_init()
{
    // Bring in all our CSS files
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'html.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'grid.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'site.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'page.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'banner.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'chat.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'header.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'footer.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'form_element.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'form_input.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'form_select.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'form_layout.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'form_button.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'form_notice.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'list.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'table.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'tabs.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'image.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'forum.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'gallery.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'profile.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'skin.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'slider.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'text.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'doc.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'poll.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'slidebar.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'admin_menu.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'admin_log.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'admin_modal.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'tablet.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'mobile.css');

    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'playlist.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'bundle.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'base.css');
    jrCore_register_module_feature('jrCore', 'css', 'jrSage', 'action.css');

    // Register our Javascript files with the core
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSage', 'responsiveslides.min.js');
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSage', 'jrSage.js');

    // Slidebars
    jrCore_register_module_feature('jrCore', 'javascript', 'jrSage', APP_DIR . '/skins/jrSage/contrib/slidebars/slidebars.min.js');

    // Tell the core the default icon set to use (black or white)
    jrCore_register_module_feature('jrCore', 'icon_color', 'jrSage', 'black');
    // Tell the core the size of our action buttons (width in pixels, up to 64)
    jrCore_register_module_feature('jrCore', 'icon_size', 'jrSage', 16);

    // Our default media player skins
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrSage', 'jrAudio', 'jrAudio_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrSage', 'jrVideo', 'jrVideo_blue_monday');
    jrCore_register_module_feature('jrCore', 'media_player_skin', 'jrSage', 'jrPlaylist', 'jrPlaylist_blue_monday');

    return true;
}
